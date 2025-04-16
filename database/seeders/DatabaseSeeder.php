<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
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

        User::factory()->create([
            'name' => env('ADMIN_NAME'),
            'email' => env('ADMIN_EMAIL'),
            'password' => bcrypt(env('ADMIN_PASSWORD')),
        ]);

        $this->call([
            TiposCasoSeeder::class,
            EstadosSeeder::class,
            ImagenesWebSeeder::class,
            HomePageSeeder::class,
            StatisticsPageSeeder::class,
            InformationPageSeeder::class,
        ]);
    }
}
