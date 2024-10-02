<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = User::factory()->create([
            'uuid' => Str::uuid(),
            'name' => 'Superadmin',
            'email' => 'superadmin@finance.id',
            'username' => 'superadmin',
            'password' => Hash::make("pass.123"),
            "status" => "active",
        ]);

        $superadmin->assignRole('superadmin');

        $finance1 = User::factory()->create([
            'uuid' => Str::uuid(),
            'name' => 'finance1',
            'email' => 'finance1@finance.id',
            'username' => 'finance1',
            'password' => Hash::make("pass.123"),
            "status" => "active",
        ]);

        $finance1->assignRole('finance1');

        $finance2 = User::factory()->create([
            'uuid' => Str::uuid(),
            'name' => 'finance2',
            'email' => 'finance2@finance.id',
            'username' => 'finance2',
            'password' => Hash::make("pass.123"),
            "status" => "active",
        ]);

        $finance2->assignRole('finance2');

        $finance3 = User::factory()->create([
            'uuid' => Str::uuid(),
            'name' => 'finance3',
            'email' => 'finance3@finance.id',
            'username' => 'finance3',
            'password' => Hash::make("pass.123"),
            "status" => "active",
        ]);

        $finance3->assignRole('finance3');
    }
}
