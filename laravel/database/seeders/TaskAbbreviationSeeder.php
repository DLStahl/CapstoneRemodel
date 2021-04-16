<?php

namespace Database\Seeders;

use App\Models\TaskAbbreviation;
use Illuminate\Database\Seeder;

class TaskAbbreviationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attendingTaskAbbrev = [
            'Endo 1',
            'Endo 2',
            'Endo 3',
            'Late 1',
            'Late 2',
            'Late 3',
            'Late 4',
            'Late 5',
            'Neuro1',
            'Neuro2',
            'Offsite1',
            'OR',
            'Ortho 1',
            'Pulmonary',
            'SDS-1',
            'SDS-2',
            'SDS-3',
            'T1',
            'T2',
        ];

        foreach ($attendingTaskAbbrev as $taskAbbrev) {
            // Use firstOrCreate b/c we don't care if the abbreviation is already in the database.
            TaskAbbreviation::firstOrCreate(['abbreviation' => $taskAbbrev]);
        }
    }
}
