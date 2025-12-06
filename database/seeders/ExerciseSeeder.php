<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exercises = [
            // CHEST EXERCISES
            ['name' => 'Barbell Bench Press', 'description' => 'Classic chest exercise using a barbell on a flat bench', 'muscle_group' => 'Chest', 'equipment' => 'Barbell', 'difficulty' => 'intermediate', 'instructions' => 'Lie on bench, lower bar to chest, press up'],
            ['name' => 'Dumbbell Bench Press', 'description' => 'Chest exercise using dumbbells for better range of motion', 'muscle_group' => 'Chest', 'equipment' => 'Dumbbells', 'difficulty' => 'intermediate', 'instructions' => 'Lie on bench, lower dumbbells to chest, press up'],
            ['name' => 'Push-ups', 'description' => 'Bodyweight chest exercise, great for beginners', 'muscle_group' => 'Chest', 'equipment' => 'Bodyweight', 'difficulty' => 'beginner', 'instructions' => 'Start in plank position, lower body, push back up'],
            ['name' => 'Incline Dumbbell Press', 'description' => 'Upper chest focused exercise', 'muscle_group' => 'Chest', 'equipment' => 'Dumbbells', 'difficulty' => 'intermediate', 'instructions' => 'Press dumbbells on inclined bench'],
            ['name' => 'Cable Flyes', 'description' => 'Isolation exercise for chest muscles', 'muscle_group' => 'Chest', 'equipment' => 'Cable Machine', 'difficulty' => 'intermediate', 'instructions' => 'Pull cables together in front of chest'],
            ['name' => 'Dips', 'description' => 'Bodyweight exercise targeting chest and triceps', 'muscle_group' => 'Chest', 'equipment' => 'Bodyweight', 'difficulty' => 'intermediate', 'instructions' => 'Lower body between parallel bars, push up'],

            // BACK EXERCISES
            ['name' => 'Deadlift', 'description' => 'Compound exercise targeting entire posterior chain', 'muscle_group' => 'Back', 'equipment' => 'Barbell', 'difficulty' => 'advanced', 'instructions' => 'Lift barbell from floor to standing position'],
            ['name' => 'Pull-ups', 'description' => 'Bodyweight exercise for upper back', 'muscle_group' => 'Back', 'equipment' => 'Bodyweight', 'difficulty' => 'intermediate', 'instructions' => 'Hang from bar, pull body up until chin over bar'],
            ['name' => 'Barbell Row', 'description' => 'Compound back exercise', 'muscle_group' => 'Back', 'equipment' => 'Barbell', 'difficulty' => 'intermediate', 'instructions' => 'Bend over, pull barbell to lower chest'],
            ['name' => 'Lat Pulldown', 'description' => 'Machine exercise for latissimus dorsi', 'muscle_group' => 'Back', 'equipment' => 'Cable Machine', 'difficulty' => 'beginner', 'instructions' => 'Pull bar down to upper chest'],
            ['name' => 'T-Bar Row', 'description' => 'Back exercise using T-bar row machine', 'muscle_group' => 'Back', 'equipment' => 'T-Bar Machine', 'difficulty' => 'intermediate', 'instructions' => 'Pull T-bar to chest'],
            ['name' => 'Cable Row', 'description' => 'Seated rowing exercise', 'muscle_group' => 'Back', 'equipment' => 'Cable Machine', 'difficulty' => 'beginner', 'instructions' => 'Pull cable handle to torso'],

            // SHOULDER EXERCISES
            ['name' => 'Overhead Press', 'description' => 'Shoulder press with barbell', 'muscle_group' => 'Shoulders', 'equipment' => 'Barbell', 'difficulty' => 'intermediate', 'instructions' => 'Press barbell overhead from shoulder height'],
            ['name' => 'Dumbbell Shoulder Press', 'description' => 'Shoulder exercise with dumbbells', 'muscle_group' => 'Shoulders', 'equipment' => 'Dumbbells', 'difficulty' => 'intermediate', 'instructions' => 'Press dumbbells overhead'],
            ['name' => 'Lateral Raises', 'description' => 'Isolation exercise for side delts', 'muscle_group' => 'Shoulders', 'equipment' => 'Dumbbells', 'difficulty' => 'beginner', 'instructions' => 'Raise dumbbells to sides until parallel'],
            ['name' => 'Front Raises', 'description' => 'Targets front deltoids', 'muscle_group' => 'Shoulders', 'equipment' => 'Dumbbells', 'difficulty' => 'beginner', 'instructions' => 'Raise dumbbells in front until parallel'],
            ['name' => 'Rear Delt Flyes', 'description' => 'Targets rear deltoids', 'muscle_group' => 'Shoulders', 'equipment' => 'Dumbbells', 'difficulty' => 'beginner', 'instructions' => 'Bend over, raise dumbbells to sides'],
            ['name' => 'Upright Row', 'description' => 'Compound shoulder exercise', 'muscle_group' => 'Shoulders', 'equipment' => 'Barbell', 'difficulty' => 'intermediate', 'instructions' => 'Pull barbell up to chest level'],

            // ARM EXERCISES
            ['name' => 'Barbell Curl', 'description' => 'Bicep exercise with barbell', 'muscle_group' => 'Arms', 'equipment' => 'Barbell', 'difficulty' => 'beginner', 'instructions' => 'Curl barbell to chest'],
            ['name' => 'Dumbbell Curl', 'description' => 'Bicep exercise with dumbbells', 'muscle_group' => 'Arms', 'equipment' => 'Dumbbells', 'difficulty' => 'beginner', 'instructions' => 'Curl dumbbells alternately or together'],
            ['name' => 'Hammer Curl', 'description' => 'Bicep and brachialis exercise', 'muscle_group' => 'Arms', 'equipment' => 'Dumbbells', 'difficulty' => 'beginner', 'instructions' => 'Curl with neutral grip'],
            ['name' => 'Tricep Dips', 'description' => 'Bodyweight tricep exercise', 'muscle_group' => 'Arms', 'equipment' => 'Bodyweight', 'difficulty' => 'intermediate', 'instructions' => 'Dip on bench or parallel bars'],
            ['name' => 'Tricep Pushdown', 'description' => 'Cable tricep exercise', 'muscle_group' => 'Arms', 'equipment' => 'Cable Machine', 'difficulty' => 'beginner', 'instructions' => 'Push cable down with straight arms'],
            ['name' => 'Overhead Tricep Extension', 'description' => 'Tricep isolation exercise', 'muscle_group' => 'Arms', 'equipment' => 'Dumbbells', 'difficulty' => 'beginner', 'instructions' => 'Extend dumbbell overhead'],

            // LEG EXERCISES
            ['name' => 'Squat', 'description' => 'King of leg exercises', 'muscle_group' => 'Legs', 'equipment' => 'Barbell', 'difficulty' => 'intermediate', 'instructions' => 'Lower body by bending knees, stand back up'],
            ['name' => 'Leg Press', 'description' => 'Machine leg exercise', 'muscle_group' => 'Legs', 'equipment' => 'Leg Press Machine', 'difficulty' => 'beginner', 'instructions' => 'Press weight with legs'],
            ['name' => 'Romanian Deadlift', 'description' => 'Hamstring focused exercise', 'muscle_group' => 'Legs', 'equipment' => 'Barbell', 'difficulty' => 'intermediate', 'instructions' => 'Hinge at hips, lower bar, return'],
            ['name' => 'Lunges', 'description' => 'Unilateral leg exercise', 'muscle_group' => 'Legs', 'equipment' => 'Bodyweight', 'difficulty' => 'beginner', 'instructions' => 'Step forward, lower back knee, return'],
            ['name' => 'Leg Curl', 'description' => 'Hamstring isolation', 'muscle_group' => 'Legs', 'equipment' => 'Leg Curl Machine', 'difficulty' => 'beginner', 'instructions' => 'Curl weight with legs'],
            ['name' => 'Leg Extension', 'description' => 'Quadriceps isolation', 'muscle_group' => 'Legs', 'equipment' => 'Leg Extension Machine', 'difficulty' => 'beginner', 'instructions' => 'Extend legs against resistance'],
            ['name' => 'Calf Raises', 'description' => 'Calf muscle exercise', 'muscle_group' => 'Legs', 'equipment' => 'Bodyweight', 'difficulty' => 'beginner', 'instructions' => 'Raise up on toes, lower'],

            // CORE EXERCISES
            ['name' => 'Plank', 'description' => 'Core stability exercise', 'muscle_group' => 'Core', 'equipment' => 'Bodyweight', 'difficulty' => 'beginner', 'instructions' => 'Hold body in straight line'],
            ['name' => 'Crunches', 'description' => 'Abdominal exercise', 'muscle_group' => 'Core', 'equipment' => 'Bodyweight', 'difficulty' => 'beginner', 'instructions' => 'Lift shoulders off ground'],
            ['name' => 'Russian Twists', 'description' => 'Oblique exercise', 'muscle_group' => 'Core', 'equipment' => 'Bodyweight', 'difficulty' => 'beginner', 'instructions' => 'Rotate torso side to side'],
            ['name' => 'Mountain Climbers', 'description' => 'Dynamic core exercise', 'muscle_group' => 'Core', 'equipment' => 'Bodyweight', 'difficulty' => 'intermediate', 'instructions' => 'Alternate bringing knees to chest'],
            ['name' => 'Dead Bug', 'description' => 'Core stability exercise', 'muscle_group' => 'Core', 'equipment' => 'Bodyweight', 'difficulty' => 'beginner', 'instructions' => 'Alternate opposite arm and leg'],
            ['name' => 'Bicycle Crunches', 'description' => 'Abdominal and oblique exercise', 'muscle_group' => 'Core', 'equipment' => 'Bodyweight', 'difficulty' => 'beginner', 'instructions' => 'Alternate bringing elbow to opposite knee'],

            // HOME EXERCISES (Bodyweight)
            ['name' => 'Burpees', 'description' => 'Full body bodyweight exercise', 'muscle_group' => 'Full Body', 'equipment' => 'Bodyweight', 'difficulty' => 'advanced', 'instructions' => 'Squat, jump back to plank, push-up, jump forward, jump up'],
            ['name' => 'Jumping Jacks', 'description' => 'Cardio and warm-up exercise', 'muscle_group' => 'Full Body', 'equipment' => 'Bodyweight', 'difficulty' => 'beginner', 'instructions' => 'Jump while spreading legs and raising arms'],
            ['name' => 'High Knees', 'description' => 'Cardio exercise', 'muscle_group' => 'Legs', 'equipment' => 'Bodyweight', 'difficulty' => 'beginner', 'instructions' => 'Run in place bringing knees high'],
            ['name' => 'Jump Squats', 'description' => 'Explosive leg exercise', 'muscle_group' => 'Legs', 'equipment' => 'Bodyweight', 'difficulty' => 'intermediate', 'instructions' => 'Squat then jump up'],
            ['name' => 'Wall Sit', 'description' => 'Isometric leg exercise', 'muscle_group' => 'Legs', 'equipment' => 'Bodyweight', 'difficulty' => 'intermediate', 'instructions' => 'Hold squat position against wall'],
            ['name' => 'Pike Push-ups', 'description' => 'Shoulder focused push-up variation', 'muscle_group' => 'Shoulders', 'equipment' => 'Bodyweight', 'difficulty' => 'intermediate', 'instructions' => 'Push-up in pike position'],
            ['name' => 'Diamond Push-ups', 'description' => 'Tricep focused push-up', 'muscle_group' => 'Arms', 'equipment' => 'Bodyweight', 'difficulty' => 'intermediate', 'instructions' => 'Push-up with hands in diamond shape'],
            ['name' => 'Hindu Push-ups', 'description' => 'Full body push-up variation', 'muscle_group' => 'Full Body', 'equipment' => 'Bodyweight', 'difficulty' => 'intermediate', 'instructions' => 'Push-up with flowing motion'],
            ['name' => 'Superman', 'description' => 'Lower back exercise', 'muscle_group' => 'Back', 'equipment' => 'Bodyweight', 'difficulty' => 'beginner', 'instructions' => 'Lie face down, lift arms and legs'],
            ['name' => 'Glute Bridge', 'description' => 'Glute and core exercise', 'muscle_group' => 'Legs', 'equipment' => 'Bodyweight', 'difficulty' => 'beginner', 'instructions' => 'Lift hips off ground, squeeze glutes'],

            // CARDIO EXERCISES
            ['name' => 'Running', 'description' => 'Cardiovascular exercise', 'muscle_group' => 'Cardio', 'equipment' => 'None', 'difficulty' => 'beginner', 'instructions' => 'Run at steady or interval pace'],
            ['name' => 'Jump Rope', 'description' => 'Cardio exercise with rope', 'muscle_group' => 'Cardio', 'equipment' => 'Jump Rope', 'difficulty' => 'intermediate', 'instructions' => 'Jump over rope continuously'],
            ['name' => 'Rowing Machine', 'description' => 'Full body cardio exercise', 'muscle_group' => 'Full Body', 'equipment' => 'Rowing Machine', 'difficulty' => 'intermediate', 'instructions' => 'Row with proper form'],
            ['name' => 'Elliptical', 'description' => 'Low impact cardio', 'muscle_group' => 'Cardio', 'equipment' => 'Elliptical Machine', 'difficulty' => 'beginner', 'instructions' => 'Use elliptical machine'],
            ['name' => 'Stationary Bike', 'description' => 'Low impact cardio', 'muscle_group' => 'Cardio', 'equipment' => 'Stationary Bike', 'difficulty' => 'beginner', 'instructions' => 'Cycle on stationary bike'],

            // FUNCTIONAL EXERCISES
            ['name' => 'Kettlebell Swing', 'description' => 'Hip hinge exercise', 'muscle_group' => 'Full Body', 'equipment' => 'Kettlebell', 'difficulty' => 'intermediate', 'instructions' => 'Swing kettlebell from hip height to chest level'],
            ['name' => 'Turkish Get-up', 'description' => 'Complex full body movement', 'muscle_group' => 'Full Body', 'equipment' => 'Kettlebell', 'difficulty' => 'advanced', 'instructions' => 'Get up from lying position while holding weight'],
            ['name' => 'Farmer\'s Walk', 'description' => 'Grip and core strength', 'muscle_group' => 'Full Body', 'equipment' => 'Dumbbells', 'difficulty' => 'intermediate', 'instructions' => 'Walk while carrying heavy weights'],
            ['name' => 'Battle Ropes', 'description' => 'Full body conditioning', 'muscle_group' => 'Full Body', 'equipment' => 'Battle Ropes', 'difficulty' => 'intermediate', 'instructions' => 'Wave ropes in various patterns'],
        ];

        foreach ($exercises as $exercise) {
            Exercise::firstOrCreate(
                ['name' => $exercise['name']],
                $exercise
            );
        }

        $this->command->info('Exercises seeded successfully! Total: ' . count($exercises));
    }
}

