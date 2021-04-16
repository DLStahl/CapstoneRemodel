<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MilestoneTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('milestone')->insert([
            'category' => 'PC1',
            'title' => 'Patient Care',
            'detail' => 'Pre-anesthetic patient evaluation, assessment, and prep',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PC2',
            'title' => 'Patient Care',
            'detail' => 'Anesthetic plan and conduct',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PC3',
            'title' => 'Patient Care',
            'detail' => 'Peri-procedural pain management',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PC4',
            'title' => 'Patient Care',
            'detail' => 'Management of peri-anesthetic complications',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PC5',
            'title' => 'Patient Care',
            'detail' => 'Crisis management',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PC6',
            'title' => 'Patient Care',
            'detail' => 'Triage and management of the critically-ill patient in non-op setting',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PC7',
            'title' => 'Patient Care',
            'detail' => 'Acute, chronic, and cancer-related pain cs and mgmt',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PC88',
            'title' => 'Patient Care',
            'detail' => 'Technical skills: Airway management',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PC9',
            'title' => 'Patient Care',
            'detail' => 'Technical skills: Use/Interpretation of monitoring+equipment',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PC10',
            'title' => 'Patient Care',
            'detail' => 'Technical skills: Regional anesthesia',
        ]);

        DB::table('milestone')->insert([
            'category' => 'MK1',
            'title' => 'Medical Knowledge',
            'detail' => 'Knowledge as outlined in the ABA Content Outline',
        ]);

        DB::table('milestone')->insert([
            'category' => 'SBP1',
            'title' => 'Systems-based Practice',
            'detail' => 'Coordination of patient care within the health care system',
        ]);

        DB::table('milestone')->insert([
            'category' => 'SBP2',
            'title' => 'Systems-based Practice',
            'detail' => 'Patient safety and quality improvement',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PBLI1',
            'title' => 'Practice-based Learning and Improvement',
            'detail' => 'Incorporation of QI and pt safety initiatives into personal practice',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PBLI2',
            'title' => 'Practice-based Learning and Improvement',
            'detail' => 'Analysis of practice to identify areas in need of improvement',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PBLI3',
            'title' => 'Practice-based Learning and Improvement',
            'detail' => 'Self-directed learning',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PBLI4',
            'title' => 'Practice-based Learning and Improvement',
            'detail' => 'Education of patient, families, students, residents, etc',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PRO1',
            'title' => 'Professionalism',
            'detail' => 'Responsibility to patients, families, and society',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PRO2',
            'title' => 'Professionalism',
            'detail' => 'Honesty, integrity, and ethical behavior',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PRO3',
            'title' => 'Professionalism',
            'detail' => 'Commitment to institution, department, and colleagues',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PRO4',
            'title' => 'Professionalism',
            'detail' => 'Receiving and giving feedback',
        ]);

        DB::table('milestone')->insert([
            'category' => 'PRO5',
            'title' => 'Professionalism',
            'detail' => 'Responsibility to maintain personal emotional/physical/mental health',
        ]);

        DB::table('milestone')->insert([
            'category' => 'ICS1',
            'title' => 'Interpersonal and Communication Skills',
            'detail' => 'Communication with patients and families',
        ]);

        DB::table('milestone')->insert([
            'category' => 'ICS2',
            'title' => 'Interpersonal and Communication Skills',
            'detail' => 'Communication with other professionals',
        ]);

        DB::table('milestone')->insert([
            'category' => 'ICS3',
            'title' => 'Interpersonal and Communication Skills',
            'detail' => 'Team and leadership skills',
        ]);
    }
}
