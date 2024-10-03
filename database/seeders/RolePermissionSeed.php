<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as ModelsRole;

class RolePermissionSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
            $role_superadmin = ModelsRole::create(
                ['name' => 'superadmin'],
            );
            $role_finance1 = ModelsRole::create(
                ['name' => 'finance1'],
            );
            $role_finance2 = ModelsRole::create(
                ['name' => 'finance2'],
            );
            $role_finance3 = ModelsRole::create(
                ['name' => 'finance3'],
            );

            $permission_view_user = Permission::create(["name" => 'view-user']);
            $permission_create_user = Permission::create(["name" => 'create-user']);
            $permission_update_user = Permission::create(["name" => 'update-user']);
            $permission_delete_user = Permission::create(["name" => 'delete-user']);
            $permission_delete_user = Permission::create(["name" => 'activated-user']);
            $permission_view_role = Permission::create(["name" => 'view-role']);
            $permission_create_role = Permission::create(["name" => 'create-role']);
            $permission_update_role = Permission::create(["name" => 'update-role']);
            $permission_delete_role = Permission::create(["name" => 'delete-role']);
            $permission_view_role = Permission::create(["name" => 'view-permission']);
            $permission_create_role = Permission::create(["name" => 'create-permission']);
            $permission_update_role = Permission::create(["name" => 'update-permission']);
            $permission_delete_role = Permission::create(["name" => 'delete-permission']);
            $permission_view_channel = Permission::create(["name" => 'view-channel']);
            $permission_create_channel = Permission::create(["name" => 'create-channel']);
            $permission_update_channel = Permission::create(["name" => 'update-channel']);
            $permission_delete_channel = Permission::create(["name" => 'delete-channel']);
            $permission_view_param = Permission::create(["name" => 'view-param']);
            $permission_create_param = Permission::create(["name" => 'create-param']);
            $permission_update_param = Permission::create(["name" => 'update-param']);
            $permission_delete_param = Permission::create(["name" => 'delete-param']);
            $permission_view_bs = Permission::create(["name" => 'view-bs']);
            $permission_create_bs = Permission::create(["name" => 'create-bs']);
            $permission_update_bs = Permission::create(["name" => 'update-bs']);
            $permission_delete_bs = Permission::create(["name" => 'delete-bs']);
            $permission_view_reconlist = Permission::create(["name" => 'view-reconlist']);
            $permission_create_reconlist = Permission::create(["name" => 'create-reconlist']);
            $permission_update_reconlist = Permission::create(["name" => 'update-reconlist']);
            $permission_delete_reconlist = Permission::create(["name" => 'delete-reconlist']);
            $permission_download_reconlist = Permission::create(["name" => 'download-reconlist']);
            $permission_download_reconlist = Permission::create(["name" => 'checker-reconlist']);
            $permission_download_reconlist = Permission::create(["name" => 'auto-reconlist']);
            $permission_download_reconlist = Permission::create(["name" => 'manual-reconlist']);
            $permission_view_disburslist = Permission::create(["name" => 'view-disburslist']);
            $permission_view_disburslist = Permission::create(["name" => 'download-disburslist']);
            $permission_approve_disburslist = Permission::create(["name" => 'approve-disburslist']);
            $permission_cancel_disburslist = Permission::create(["name" => 'cancel-disburslist']);
            $permission_view_unmatchlist = Permission::create(["name" => 'view-unmatchlist']);
            $permission_download_unmatchlist = Permission::create(["name" => 'download-unmatchlist']);

            $role_superadmin->givePermissionTo([
                'view-user',
                'create-user',
                'update-user',
                'delete-user',
                'activated-user',
                'view-role',
                'create-role',
                'update-role',
                'delete-role',
                'view-permission',
                'create-permission',
                'update-permission',
                'delete-permission',
                'view-channel',
                'create-channel',
                'update-channel',
                'delete-channel',
                'view-param',
                'create-param',
                'update-param',
                'delete-param',
                'view-bs',
                'create-bs',
                'update-bs',
                'delete-bs',
                'view-reconlist',
                'create-reconlist',
                'update-reconlist',
                'delete-reconlist',
                'download-reconlist',
                'checker-reconlist',
                'auto-reconlist',
                'manual-reconlist',
                'view-disburslist',
                'download-disburslist',
                'approve-disburslist',
                'cancel-disburslist',
                'view-unmatchlist',
                'download-unmatchlist',
            ]);
            $role_finance1->givePermissionTo([
                'view-user',
                'create-user',
                'update-user',
                'delete-user',
                'activated-user',
                'view-role',
                'create-role',
                'update-role',
                'delete-role',
                'view-permission',
                'create-permission',
                'update-permission',
                'delete-permission',
                'view-channel',
                'create-channel',
                'update-channel',
                'delete-channel',
                'view-param',
                'create-param',
                'update-param',
                'delete-param',
                'view-bs',
                'create-bs',
                'update-bs',
                'delete-bs',
                'view-reconlist',
                'create-reconlist',
                'update-reconlist',
                'delete-reconlist',
                'download-reconlist',
                'checker-reconlist',
                'auto-reconlist',
                'manual-reconlist',
                'view-disburslist',
                'download-disburslist',
                'approve-disburslist',
                'cancel-disburslist',
                'view-unmatchlist',
                'download-unmatchlist',
            ]);
            $role_finance2->givePermissionTo([
                'view-user',
                'create-user',
                'update-user',
                'delete-user',
                'activated-user',
                'view-role',
                'create-role',
                'update-role',
                'delete-role',
                'view-permission',
                'create-permission',
                'update-permission',
                'delete-permission',
                'view-channel',
                'create-channel',
                'update-channel',
                'delete-channel',
                'view-param',
                'create-param',
                'update-param',
                'delete-param',
                'view-bs',
                'create-bs',
                'update-bs',
                'delete-bs',
                'view-reconlist',
                'create-reconlist',
                'update-reconlist',
                'delete-reconlist',
                'download-reconlist',
                'checker-reconlist',
                'auto-reconlist',
                'manual-reconlist',
                'download-disburslist',
                'view-disburslist',
                'approve-disburslist',
                'cancel-disburslist',
                'view-unmatchlist',
                'download-unmatchlist',
            ]);
            $role_finance3->givePermissionTo([
                'view-user',
                'create-user',
                'update-user',
                'delete-user',
                'activated-user',
                'view-role',
                'create-role',
                'update-role',
                'delete-role',
                'view-permission',
                'create-permission',
                'update-permission',
                'delete-permission',
                'view-channel',
                'create-channel',
                'update-channel',
                'delete-channel',
                'view-param',
                'create-param',
                'update-param',
                'delete-param',
                'view-bs',
                'create-bs',
                'update-bs',
                'delete-bs',
                'view-reconlist',
                'create-reconlist',
                'update-reconlist',
                'delete-reconlist',
                'download-reconlist',
                'checker-reconlist',
                'auto-reconlist',
                'manual-reconlist',
                'download-disburslist',
                'view-disburslist',
                'approve-disburslist',
                'cancel-disburslist',
                'view-unmatchlist',
                'download-unmatchlist',
            ]);
    }
}
