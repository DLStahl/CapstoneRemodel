<?php

use Illuminate\Database\Seeder;

class FilterRotationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (file_exists(__DIR__ . "/../../../resources/database/SurgeonRotations.csv")) {
            /**
             * Read data from the backup file and add into database
             */
            $fp = fopen(__DIR__ . "/../../../resources/database/SurgeonRotations.csv", "r");

            // Read the first row
            fgetcsv($fp);

            // Read rows until null
            while (($line = fgetcsv($fp)) !== false) {
                $surgeon = $line[0];
                $rotation = $line[2];
                DB::table("filter_rotation")->insert(["surgeon" => $surgeon, "rotation" => $rotation]);
            }

            // Close file
            fclose($fp);
        }
    }
}
