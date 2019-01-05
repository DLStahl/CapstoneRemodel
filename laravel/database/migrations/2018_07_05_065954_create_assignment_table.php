<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAssignmentTable extends Migration
{
    /**
     * Initialize data in the table.
     *
     * @return void
     */
    private function initialize()
    {
        if (file_exists ( __DIR__.$_ENV["BACKUP_PATH"]."assignment.csv" )) {

            /**
             * Read data from the backup file and add into database
             */
            $fp = fopen(__DIR__.$_ENV["BACKUP_PATH"]."assignment.csv", 'r');
            
            // Read the first row
            fgetcsv($fp);

            // Read rows until null
            while (($line = fgetcsv($fp)) !== false)
            {
                $id = $line[0];
                $date = $line[1];
                $resident = $line[2];
                $schedule = $line[3];
                $attending = $line[4];
                
                DB::table('assignment')->insert([
                    'id' => $id, 
                    'date' => $date,
                    'resident' => $resident,
                    'schedule' => $schedule,
                    'attending' => $attending
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
        $filename = __DIR__.$_ENV["BACKUP_PATH"]."assignment.csv";
        $data = DB::table('assignment')->get();
        
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
            'resident',
            'schedule',
            'attending'
        ));
        // Add all rows
        foreach ($data as $info) {
            fputcsv($output, array(
                $info['id'],
                $info['date'],
                $info['resident'],
                $info['schedule'],
                $info['attending']
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
        Schema::create('assignment', function (Blueprint $table) {
            
            // Primary Key
            $table->increments('id');

            $table->date('date'); // Date
            $table->unsignedInteger('resident'); // ID of the resident
            $table->unsignedInteger('schedule')->unique(); // ID of the schedule           
            $table->string('attending');     // ID of the attending
            
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

        Schema::dropIfExists('assignment');
    }
}
