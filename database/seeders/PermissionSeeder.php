<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'household', 'description' => 'Access to Household module'],
            ['name' => 'patients', 'description' => 'Access to Patients module'],
            ['name' => 'consultations', 'description' => 'Access to Consultations module'],
            ['name' => 'immunizations', 'description' => 'Access to Immunizations module'],
            ['name' => 'medicines', 'description' => 'Access to Medicines module'],
            ['name' => 'reports', 'description' => 'Access to Reports module'],
            ['name' => 'users', 'description' => 'Access to User Management'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
