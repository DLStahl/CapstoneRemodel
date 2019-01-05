<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ScheduleParser;
use App\Status;

class UpdateScheduleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:schedule_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = date("Y-m-d", strtotime('today'));
        if (Status::where('date', $date)->doesntExist()) {
            Status::insert([
                'date' => $date
            ]);
        }

        if ((int)Status::where('date', $date)->value('schedule') < 1) {


            $parser = new ScheduleParser(date("o", strtotime('today')).date("m", strtotime('today')).date("d", strtotime('today')), true);


            Status::where('date', $date)->update([
                'schedule'=>true
            ]);

        }
    }
}

