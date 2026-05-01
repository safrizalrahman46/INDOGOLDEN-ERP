<?php

namespace App\Services;

use App\Enums\BranchRequestItemStatus;
use App\Enums\BranchRequestStatus;
use App\Models\BranchRequest;
use App\Models\BranchRequestItem;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BranchRequestService
{
    public function prepareDraft(BranchRequest $request, User $actor): BranchRequest
    {
        $request->fill([
            'status' => BranchRequestStatus::Draft->value,
            'created_by' => $request->created_by ?? $actor->id,
            'branch_id' => $request->branch_id ?? $actor->branch_id,
            'request_date' => $request->request_date ?? now()->toDateString(),
            'delivery_date' => $request->delivery_date ?? now()->addDay()->toDateString(),
        ]);

        if (blank($request->request_number)) {
            $request->request_number = $this->makeNumber();
        }

        $request->save();

        return $request->refresh();
    }

    public function submit(BranchRequest $request, User $actor): BranchRequest
    {
        if ($request->items()->count() === 0) {
            throw new \RuntimeException('Tambahkan minimal 1 item request sebelum submit.');
        }

        return DB::transaction(function () use ($request, $actor): BranchRequest {
            $before = $request->toArray();

            $request->update([
                'status' => BranchRequestStatus::Submitted->value,
                'submitted_at' => now(),
            ]);

            $request->items()->where('item_status', BranchRequestItemStatus::Requested->value)->update([
                'item_status' => BranchRequestItemStatus::Requested->value,
            ]);

            app(ActivityLogService::class)->log(
                module: 'branch_request',
                action: 'submit',
                subject: $request,
                before: $before,
                after: $request->fresh()->toArray(),
                actor: $actor,
                branchId: $request->branch_id,
                description: 'Cabang submit request barang',
            );

            $this->notifyWarehouse('Request baru dari cabang '.$request->branch?->name, $request);

            return $request->refresh();
        });
    }

    public function review(BranchRequest $request, User $actor): BranchRequest
    {
        return $this->updateStatus($request, BranchRequestStatus::Reviewed, $actor, ['reviewed_by' => $actor->id, 'reviewed_at' => now()], 'Gudang review request cabang');
    }

    public function approve(BranchRequest $request, User $actor): BranchRequest
    {
        return DB::transaction(function () use ($request, $actor): BranchRequest {
            foreach ($request->items as $item) {
                if ((float) $item->approved_qty <= 0) {
                    $item->update([
                        'approved_qty' => $item->requested_qty,
                    ]);
                }

                $item->update([
                    'item_status' => BranchRequestItemStatus::Approved->value,
                ]);
            }

            $updated = $this->updateStatus(
                $request,
                BranchRequestStatus::Approved,
                $actor,
                ['approved_by' => $actor->id, 'approved_at' => now()],
                'Gudang approve request cabang',
                false,
            );

            $this->notifyBranch($updated, 'Request '.$updated->request_number.' disetujui gudang.');

            return $updated;
        });
    }

    public function reject(BranchRequest $request, User $actor, ?string $note = null): BranchRequest
    {
        $updated = $this->updateStatus(
            $request,
            BranchRequestStatus::Rejected,
            $actor,
            [
                'note_warehouse' => $note ?? $request->note_warehouse,
            ],
            'Gudang reject request cabang',
            false,
        );

        $this->notifyBranch($updated, 'Request '.$updated->request_number.' ditolak gudang.');

        return $updated;
    }

    public function markPacked(BranchRequest $request, User $actor): BranchRequest
    {
        return DB::transaction(function () use ($request, $actor): BranchRequest {
            foreach ($request->items as $item) {
                $packed = max((float) $item->packed_qty, (float) $item->approved_qty);
                $item->update([
                    'packed_qty' => $packed,
                    'item_status' => BranchRequestItemStatus::Packed->value,
                ]);
            }

            return $this->updateStatus($request, BranchRequestStatus::Packed, $actor, ['packed_at' => now()], 'Gudang selesai packing request cabang');
        });
    }

    public function markShipped(BranchRequest $request, User $actor): BranchRequest
    {
        return DB::transaction(function () use ($request, $actor): BranchRequest {
            foreach ($request->items as $item) {
                $shipped = max((float) $item->shipped_qty, (float) $item->packed_qty, (float) $item->approved_qty);
                $item->update([
                    'shipped_qty' => $shipped,
                    'item_status' => BranchRequestItemStatus::Shipped->value,
                ]);
            }

            $updated = $this->updateStatus(
                $request,
                BranchRequestStatus::Shipped,
                $actor,
                ['shipped_by' => $actor->id, 'shipped_at' => now()],
                'Gudang kirim request cabang',
                false,
            );

            $this->notifyBranch($updated, 'Barang untuk request '.$updated->request_number.' telah dikirim.');

            return $updated;
        });
    }

    public function markReceived(BranchRequest $request, User $actor): BranchRequest
    {
        return DB::transaction(function () use ($request, $actor): BranchRequest {
            foreach ($request->items as $item) {
                $received = max((float) $item->received_qty, (float) $item->shipped_qty);
                $item->update([
                    'received_qty' => $received,
                    'item_status' => BranchRequestItemStatus::Received->value,
                ]);
            }

            return $this->updateStatus(
                $request,
                BranchRequestStatus::Received,
                $actor,
                ['received_by' => $actor->id, 'received_at' => now()],
                'Cabang terima request barang',
            );
        });
    }

    protected function updateStatus(
        BranchRequest $request,
        BranchRequestStatus $status,
        User $actor,
        array $extra = [],
        string $description = '',
        bool $notifyWarehouse = true,
    ): BranchRequest {
        $before = $request->toArray();

        $request->update(array_merge(['status' => $status->value], $extra));

        app(ActivityLogService::class)->log(
            module: 'branch_request',
            action: $status->value,
            subject: $request,
            before: $before,
            after: $request->fresh()->toArray(),
            actor: $actor,
            branchId: $request->branch_id,
            description: $description,
        );

        if ($notifyWarehouse) {
            $this->notifyWarehouse('Update request '.$request->request_number.' menjadi '.str($status->value)->replace('_', ' ')->title(), $request);
        }

        return $request->refresh();
    }

    protected function makeNumber(): string
    {
        $prefix = 'REQ-'.now()->format('Ymd');
        $last = BranchRequest::query()
            ->where('request_number', 'like', $prefix.'-%')
            ->latest('id')
            ->value('request_number');

        $next = 1;
        if (is_string($last) && str_contains($last, '-')) {
            $next = (int) str($last)->afterLast('-')->toString() + 1;
        }

        return sprintf('%s-%04d', $prefix, $next);
    }

    protected function notifyWarehouse(string $message, BranchRequest $request): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        $users = User::query()->whereHas('roles', function ($query): void {
            $query->whereIn('name', ['gudang', 'head_logistics', 'logistics_admin', 'admin', 'owner']);
        })->get();

        foreach ($users as $user) {
            Notification::make()
                ->title('Branch Request Update')
                ->body($message)
                ->sendToDatabase($user);
        }
    }

    protected function notifyBranch(BranchRequest $request, string $message): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        $users = User::query()
            ->where('branch_id', $request->branch_id)
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['cabang', 'branch']))
            ->get();

        foreach ($users as $user) {
            Notification::make()
                ->title('Status Request Barang')
                ->body($message)
                ->sendToDatabase($user);
        }
    }
}
