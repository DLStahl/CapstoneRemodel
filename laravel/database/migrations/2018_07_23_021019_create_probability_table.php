<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateProbabilityTable extends Migration
{
    /**
     * Initialize data in the table.
     *
     * @return void
     */
    private function initialize()
    {
        if (file_exists ( __DIR__.$_ENV["BACKUP_PATH"]."probability.csv" )) {

            /**
             * Read data from the backup file and add into database
             */
            $fp = fopen(__DIR__.$_ENV["BACKUP_PATH"]."probability.csv", 'r');
            
            // Read the first row
            fgetcsv($fp);

            // Read rows until null
            while (($line = fgetcsv($fp)) !== false)
            {
                $id = $line[0];
                $resident = $line[1];
                $total = $line[2];
                $selected = $line[3];
                $probability = $line[4];
                
                DB::table('probability')->insert([
                    'id' => $id, 
                    'resident' => $resident,
                    'total' => $total,
                    'selected' => $selected,
                    'probability' => $probability
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
        $filename = __DIR__.$_ENV["BACKUP_PATH"]."probability.csv";
        $data = DB::table('probability')->get();
        
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
            'resident',
            'total',
            'selected',
            'probability'
        ));
        // Add all rows
        foreach ($data as $info) {
            fputcsv($output, array(
                $info['id'],
                $info['resident'],
                $info['total'],
                $info['selected'],
                $info['probability']
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
        Schema::create('probability', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('resident');
            $table->unsignedInteger('total');            
            $table->unsignedInteger('selected');
            $table->double('probability', 15, 11);
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
        Schema::dropIfExists('probability');
    }
}
