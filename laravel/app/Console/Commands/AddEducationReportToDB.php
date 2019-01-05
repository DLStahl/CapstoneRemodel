<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ScheduleParser;

class AddEducationReportToDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'educationReport:add {date} {--process=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test data insertion from .csv file into database table.';

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
        $parser = new ScheduleParser($this->argument('date'), true);
        $process_date = $this->option('process');
        foreach($process_date as $date)
        {
            $parser->processScheduleData($date);
        }
    }
}
