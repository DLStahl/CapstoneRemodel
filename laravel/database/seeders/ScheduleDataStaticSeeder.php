<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScheduleDataStatic;

class ScheduleDataStaticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allStaticData = [
            ['MRI', 'MRI', 'MRI Procedure'],
            ['ECT', 'ECT', 'ECT Procedure'],
            ['Endoscopy', 'Endo5', 'Endoscopy Procedure'],
            ['IR', 'IR2', 'IR Procedure'],
            ['Pulmonary', 'Pulmonary', 'Bronchoscopy Procedure'],
        ];
        foreach ($allStaticData as $data) {
            ScheduleDataStatic::firstOrCreate([
                'location' => $data[0],
                'room' => $data[1],
                'case_procedure' => $data[2],
                'case_procedure_code' => '-1',
                'lead_surgeon' => 'OORA',
                'lead_surgeon_code' => '-1',
                'patient_class' => 'TBD',
                'start_time' => '07:00:00',
                'end_time' => '17:00:00',
                'rotation' => 'OORA',
            ]);
        }
    }
}
