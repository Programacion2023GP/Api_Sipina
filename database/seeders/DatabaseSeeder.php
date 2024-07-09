<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        DB::table('users')->insert([
            'numberNomina' => 000000,
            'name' => 'Administrator',
            'lastName' => 'Administrator',
            'secondSurname' => 'Administrator',
            'typeUser' => 1,
            'email' => 'admin@gomezpalacio.gob.mx',
            'password' => Hash::make('desarrollo'),
            'institution_id' => null,
            'active' => 1,

        ]);
    }
}
