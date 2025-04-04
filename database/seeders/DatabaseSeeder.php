<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clean up the duplicate medication_adherence_repeat series
        DB::table('assessment_questions')
            ->where('series_id', 'medication_adherence_repeat')
            ->delete();

        DB::table('assessment_series')
            ->where('series_id', 'medication_adherence_repeat')
            ->delete();

        // Run the seeders
        $this->call([
            AssessmentSeriesSeeder::class,
            CardiacRiskFactorsSeeder::class,
            MedicalDocumentsSeeder::class,
            CardiacSymptomsSeeder::class,
            PhysicalActivitySeeder::class,
            DietaryHabitsSeeder::class,
            ConsultationReasonsSeeder::class,
            DiagnosticPreferencesSeeder::class,
            MedicationAdherenceSeeder::class,
            StressAndSleepSeeder::class,
            // Add other seeders here as needed
        ]);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
