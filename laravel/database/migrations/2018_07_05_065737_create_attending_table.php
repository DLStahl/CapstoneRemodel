<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAttendingTable extends Migration
{
    /**
     * Initialize data in the table.
     *
     * @return void
     */
    private function initialize()
    {
        if (file_exists ( __DIR__.$_ENV["BACKUP_PATH"]."attending.csv" )) {

            /**
             * Read data from the backup file and add into database
             */
            $fp = fopen(__DIR__.$_ENV["BACKUP_PATH"]."attending.csv", 'r');
            
            // Read the first row
            fgetcsv($fp);

            // Read rows until null
            while (($line = fgetcsv($fp)) !== false)
            {
                $id = $line[0];
                $name = $line[1];
                $email = $line[2];
                $exists = $line[3];
                DB::table('attending')->insert(
                    ['id' => $id, 'name' => $name, 'email' => $email, 'exists' => $exists]
                );
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
        $filename = __DIR__.$_ENV["BACKUP_PATH"]."attending.csv";
        $data = DB::table('attending')->get();
        
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
            'name', 
            'email',
            'exists'
        ));
        // Add all rows
        foreach ($data as $info) {
            fputcsv($output, array(
                $info['id'],
                $info['name'],
                $info['email'],
                $info['exists']
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
        Schema::create('attending', function (Blueprint $table) {

            // Primary Key
            $table->string('id')->primary();
            
            $table->string('name'); // Name of the attending
            $table->string('email')->unique()->nullable(); // Email address of the attending
            $table->boolean('exists')->default(1);
            
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
        Schema::dropIfExists('attending');
    }
}
