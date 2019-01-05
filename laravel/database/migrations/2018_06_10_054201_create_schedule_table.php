<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateScheduleTable extends Migration
{
    /**
     * Initialize data in the table.
     *
     * @return void
     */
    private function initialize()
    {
        if (file_exists ( __DIR__.$_ENV["BACKUP_PATH"]."schedule_data.csv" )) {

            /**
             * Read data from the backup file and add into database
             */
            $fp = fopen(__DIR__.$_ENV["BACKUP_PATH"]."schedule_data.csv", 'r');
            
            // Read the first row
            fgetcsv($fp);

            // Read rows until null
            while (($line = fgetcsv($fp)) !== false)
            {
                $id = $line[0];
                $date = $line[1];
                $location = $line[2];
                $room = $line[3];
                $case_procedure = $line[4];
                $lead_surgeon = $line[5];
                $patient_class = $line[6];
                $start_time = $line[7];
                $end_time = $line[8];
                
                DB::table('schedule_data')->insert([
                    'id' => $id, 
                    'date' => $date,
                    'location' => $location,
                    'room' => $room,
                    'case_procedure' => $case_procedure,
                    'lead_surgeon' => $lead_surgeon,
                    'patient_class' => $patient_class,
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ]);
            }

            // Close file
            fclose($fp);

            return;
        }
    }

    /**
     * Backup data in the table.
     *
     * @return void
     */
    private function backup()
    {
        /** 
         * Save data sets into a csv file
         */        
        $filename = __DIR__.$_ENV["BACKUP_PATH"]."schedule_data.csv";
        $data = DB::table('schedule_data')->get();
        
        // Erase existing file
        if (file_exists ( $filename )) {
            $output = fopen($filename, 'w');
        }
        else {
            $output = fopen($filename, 'x');
        }
        
        // Set up the first row
        fputcsv($output, array(
            'id', 
            'date',
            'location',
            'room',
            'case_procedure',
            'lead_surgeon',
            'patient_class',
            'start_time',
            'end_time'
        ));
        // Add all rows
        foreach ($data as $info) {
            fputcsv($output, array(
                $info['id'],
                $info['date'],
                $info['location'],
                $info['room'],
                $info['case_procedure'],
                $info['lead_surgeon'],
                $info['patient_class'],
                $info['start_time'],
                $info['end_time']
            ));
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
        Schema::create('schedule_data', function (Blueprint $table) {

            // Primary Key
            $table->increments('id');

            $table->date('date'); // Date of the schedule
            $table->text('location')->nullable(); // Location of the surgery
            $table->text('room')->nullable();  // Room of the surgery
            $table->longText('case_procedure')->nullable();  // Case procedure of the surgery
            $table->text('lead_surgeon')->nullable();   // Lead surgeon of the surgery
            $table->longText('patient_class')->nullable();  // Patient class of the surgery
            $table->time('start_time')->nullable(); // Start time of the surgery
            $table->time('end_time')->nullable();   // End time of the surgery

            // Add for future extension
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
        Schema::dropIfExists('schedule_data');
    }
}
