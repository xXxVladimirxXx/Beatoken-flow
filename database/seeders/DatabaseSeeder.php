<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $classes = [
            UsersTableSeeder::class,
            RolesTableSeeder::class,
            SettingsSeeder::class,
        ];

        foreach ($classes as $class) {
            $this->call($class);
        }
    }
}
