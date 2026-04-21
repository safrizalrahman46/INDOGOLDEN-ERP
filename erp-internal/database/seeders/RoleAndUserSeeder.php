<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (UserRole::cases() as $role) {
            Role::findOrCreate($role->value, 'web');
        }

        $hq = Branch::query()->where('code', 'BR-HQ')->first();
        $jkt = Branch::query()->where('code', 'BR-JKT')->first();
        $bks = Branch::query()->where('code', 'BR-BKS')->first();

        $users = [
            [
                'name' => 'ERP Owner',
                'email' => 'owner@erp.local',
                'role' => UserRole::Owner,
                'branch_id' => $hq?->id,
            ],
            [
                'name' => 'ERP Finance',
                'email' => 'finance@erp.local',
                'role' => UserRole::Finance,
                'branch_id' => $hq?->id,
            ],
            [
                'name' => 'Head Logistics',
                'email' => 'headlogistik@erp.local',
                'role' => UserRole::HeadLogistics,
                'branch_id' => $hq?->id,
            ],
            [
                'name' => 'Admin Logistics',
                'email' => 'adminlogistik@erp.local',
                'role' => UserRole::LogisticsAdmin,
                'branch_id' => $hq?->id,
            ],
            [
                'name' => 'Cabang Jakarta',
                'email' => 'cabang.jakarta@erp.local',
                'role' => UserRole::Branch,
                'branch_id' => $jkt?->id,
            ],
            [
                'name' => 'Cabang Bekasi',
                'email' => 'cabang.bekasi@erp.local',
                'role' => UserRole::Branch,
                'branch_id' => $bks?->id,
            ],
        ];

        foreach ($users as $entry) {
            $user = User::query()->updateOrCreate(
                ['email' => $entry['email']],
                [
                    'name' => $entry['name'],
                    'branch_id' => $entry['branch_id'],
                    'phone' => '08'.random_int(1000000000, 9999999999),
                    'is_active' => true,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );

            $user->syncRoles([$entry['role']->value]);
        }
    }
}
