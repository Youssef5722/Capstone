<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::table('levels')->count() > 0) {
            return;
        }

        DB::table('levels')->insert([
            ['name' => 'Level 2', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Level 4', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
