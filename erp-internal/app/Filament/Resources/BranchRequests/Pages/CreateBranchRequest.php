<?php

namespace App\Filament\Resources\BranchRequests\Pages;

use App\Enums\BranchRequestStatus;
use App\Filament\Resources\BranchRequests\BranchRequestResource;
use App\Models\User;
use App\Services\BranchRequestService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBranchRequest extends CreateRecord
{
    protected static string $resource = BranchRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        if ($user instanceof User) {
            $data['created_by'] = $user->id;
            if ($user->isBranchLike()) {
                $data['branch_id'] = $user->branch_id;
            }
        }

        $data['request_number'] = $data['request_number'] ?? 'REQ-'.now()->format('YmdHis');
        $data['status'] = $data['status'] ?? BranchRequestStatus::Draft->value;

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = Auth::user();

        if ($user instanceof User) {
            app(BranchRequestService::class)->prepareDraft($this->record, $user);
        }
    }
}
