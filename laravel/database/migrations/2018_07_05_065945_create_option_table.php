<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateOptionTable extends Migration
{
    /**
     * Initialize data in the table.
     *
     * @return void
     */
    private function initialize()
    {
        if (file_exists ( __DIR__.$_ENV["BACKUP_PATH"]."option.csv" )) {

            /**
             * Read data from the backup file and add into database
             */
            $fp = fopen(__DIR__.$_ENV["BACKUP_PATH"]."option.csv", 'r');
            
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
                $option = $line[5];
                $milestones = $line[6];
                $objectives = $line[7];
                
                DB::table('option')->insert([
                    'id' => $id, 
                    'date' => $date,
                    'resident' => $resident,
                    'schedule' => $schedule,
                    'attending' => $attending,
                    'option' => $option,
                    'milestones' => $milestones,
                    'objectives' => $objectives
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
        $filename = __DIR__.$_ENV["BACKUP_PATH"]."option.csv";
        $data = DB::table('option')->get();
        
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
            'attending',
            'option',
            'milestones',
            'objectives'
        ));
        // Add all rows
        foreach ($data as $info) {
            fputcsv($output, array(
                $info['id'],
                $info['date'],
                $info['resident'],
                $info['schedule'],
                $info['attending'],
                $info['option'],
                $info['milestones'],
                $info['objectives']
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
        Schema::create('option', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->unsignedInteger('resident');
            $table->unsignedInteger('schedule');            
            $table->string('attending');
            $table->unsignedInteger('option');
            $table->longText('milestones')->nullable();
            $table->longText('objectives')->nullable();          
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

        Schema::dropIfExists('option');
    }
}
