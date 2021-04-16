<?php

namespace App\Console\Commands;

use App\Models\Status;
use App\AutoAssignment;
use Illuminate\Console\Command;

class AutoAssign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoassign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auto assign';

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
        //       Log::info('It works');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        if (Status::where('date', $tomorrow)->doesntExist()) {
            Status::insert([
                'date' => $tomorrow,
            ]);
        }
        if ((int) Status::where('date', $tomorrow)->value('assignment') != 1) {
            AutoAssignment::assignment($tomorrow);
            Status::where('date', $tomorrow)->update([
                'assignment' => 1,
            ]);
        }
    }
}
