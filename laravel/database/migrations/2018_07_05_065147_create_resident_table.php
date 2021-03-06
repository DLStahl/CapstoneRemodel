<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateResidentTable extends Migration
{
    /**
     * Initialize data in the table.
     *
     * @return void
     */
    private function initialize()
    {
        if (file_exists(__DIR__ . $_ENV['BACKUP_PATH'] . 'resident.csv')) {
            // Read data from the backup file and add into database
            $fp = fopen(__DIR__ . $_ENV['BACKUP_PATH'] . 'resident.csv', 'r');

            // Read the first row
            fgetcsv($fp);

            // Read rows until null
            while (($line = fgetcsv($fp)) !== false) {
                $id = $line[0];
                $name = $line[1];
                $email = $line[2];
                $exists = $line[3];
                DB::table('resident')->insert(['id' => $id, 'name' => $name, 'email' => $email, 'exists' => $exists]);
            }

            // Close file
            fclose($fp);

            return;
        }

        if (file_exists(__DIR__ . $_ENV['RESIDENT_PATH'])) {
            $fp = fopen(__DIR__ . $_ENV['RESIDENT_PATH'], 'r');
            // Read rows until null
            while (($line = fgetcsv($fp)) !== false) {
                $id = $line[0];
                $name = $line[2];
                $email = $line[1];
                $exists = $line[3];
                DB::table('resident')->insert(['id' => $id, 'name' => $name, 'email' => $email, 'exists' => $exists]);
            }
            // Close file
            fclose($fp);
        }
    }

    /**
     * Backup data in the table.
     *
     * @return void
     */
    private function backup()
    {
        // Save data sets into a csv file
        $filename = __DIR__ . $_ENV['BACKUP_PATH'] . 'resident.csv';
        $data = DB::table('resident')->get();

        // Erase existing file
        $output = fopen($filename, file_exists($filename) ? 'w' : 'x');

        // Set up the first row
        fputcsv($output, ['id', 'name', 'email', 'exists']);
        // Add all rows
        foreach ($data as $info) {
            fputcsv($output, [$info['id'], $info['name'], $info['email'], $info['exists']]);
        }

        // Close file
        fclose($output);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resident', function (Blueprint $table) {
            // Primary Key
            $table->increments('id');

            $table->string('name'); // Name of the resident
            $table->string('email')->unique(); // Email address of the resident
            $table->boolean('exists')->default(1); // Whether the resident exists

            $table->timestamps();
        });

        self::initialize();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        self::backup();

        Schema::dropIfExists('resident');
    }
}
