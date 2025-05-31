<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Models\SuperAdmin;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

         $permissions = [
            'manage clients',
            'manage transactions',
            'manage tasks',
            'manage documents',
            'manage users',
            'view reports',
            'access settings',
            'view own transactions',
            'upload documents',
            'view status updates',
            'send inquiries'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        $roles = [
            'Frontline Liaison Officer' => ['manage clients', 'manage tasks'],
            'Main Case Handler'         => ['manage clients', 'manage transactions', 'manage tasks'],
            'Financial Officer'         => ['manage transactions', 'view reports'],
            'Executive Director'        => Permission::pluck('name')->toArray(),
            'Legal Supervisor'          => ['manage documents'],
            'Quality Assurance Officer' => ['view reports'],
            'Bank Liaison Officer'      => ['manage transactions'],
            'SuperAdmin'                => Permission::pluck('name')->toArray(),
            'Client'                    =>['view own transactions','upload documents','view status updates','send inquiries']
        ];

        foreach ($roles as $role => $perms) {
            $roleInstance = Role::firstOrCreate(
                    ['name' => $role, 'guard_name' => 'web']
                );
            $roleInstance->syncPermissions($perms);
        }

        $superAdminUser = SuperAdmin::updateOrCreate([
            'email' => 'shokrymansor123@gmail.com',
        ], [
            'name' => 'Shokry Mansour',
            'phone' => '01014001055',
            'password' => bcrypt('123456789'),
            'gender' => 'male',
        ]);
        $superAdminUser->assignRole('SuperAdmin');
    }
}
