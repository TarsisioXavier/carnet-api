<?php

namespace Database\Seeders;

use App\Models\Carnet;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Helper\ProgressBar;

class CarnetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->withProgressBar(500, function (ProgressBar $bar) {
            for ($i = 0; $i < 500; $i++) {
                $factory = fake()->randomElement([
                    Carnet::factory()->monthly(),
                    Carnet::factory()->weekly(),
                    Carnet::factory()->withDownPayment()->monthly(),
                    Carnet::factory()->withDownPayment()->weekly(),
                ]);

                $factory->create();

                $bar->advance();
            }

            $bar->finish();
        });
    }
}
