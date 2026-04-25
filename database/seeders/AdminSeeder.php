<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = \App\Models\Role::where('name', 'admin')->first();
        \App\Models\User::create([
            'name' => 'System Admin',
            'email' => 'admin@cms.local',
            'password' => \Illuminate\Support\Facades\Hash::make('Admin@1234'),
            'national_id' => 'ADMIN001',
            'role_id' => $role->id,
            'status' => 'approved'
        ]);
    }
}
