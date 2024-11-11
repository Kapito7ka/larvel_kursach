<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hall;
use Illuminate\Support\Facades\DB;

class CreateSeatsForHalls extends Command
{
    protected $signature = 'halls:create-seats';
    protected $description = 'Create seats for existing halls';

    public function handle()
    {
        $halls = Hall::all();

        foreach ($halls as $hall) {
            $this->info("Creating seats for hall #{$hall->hall_number}");

            DB::beginTransaction();

            try {
                // Перевіряємо чи є вже місця для цього залу
                $existingSeats = DB::table('seats')->where('hall_id', $hall->id)->count();

                if ($existingSeats > 0) {
                    $this->warn("Hall #{$hall->hall_number} already has seats. Skipping...");
                    continue;
                }

                $seats = [];
                for ($row = 1; $row <= 10; $row++) {
                    for ($seatNumber = 1; $seatNumber <= 10; $seatNumber++) {
                        $seats[] = [
                            'hall_id' => $hall->id,
                            'row' => $row,
                            'seat_number' => $seatNumber,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }

                DB::table('seats')->insert($seats);
                DB::commit();

                $this->info("Created " . count($seats) . " seats for hall #{$hall->hall_number}");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error creating seats for hall #{$hall->hall_number}: " . $e->getMessage());
            }
        }

        $this->info('Finished creating seats for all halls');
    }
}
