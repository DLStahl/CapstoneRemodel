<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FilterRotationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Make sure the file containing FilterRotation data exists. If it does not, print a warning and return.
        if (!file_exists(__DIR__ . '/../../../resources/database/SurgeonRotations.csv')) {
            Log::warning("resources/database/SurgeonRotations.csv not found. Can't seed Filter Rotation Table.");
            return;
        }

        // Read data from the backup file and add into database
        $fp = fopen(__DIR__ . '/../../../resources/database/SurgeonRotations.csv', 'r');

        // Read the first row
        fgetcsv($fp);

        // Read rows until null
        while (($line = fgetcsv($fp)) !== false) {
            $surgeon = $line[0];
            $rotation = $line[2];
            DB::table('filter_rotation')->insert([
                'surgeon' => $surgeon,
                'rotation' => $rotation,
            ]);
        }

        // Close file
        fclose($fp);
    }
}
