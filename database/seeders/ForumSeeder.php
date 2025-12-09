<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Topic;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create users
        $admin = User::firstOrCreate(
            ['email' => 'admin@verve.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@verve.com',
                'password' => bcrypt('password'),
                'phone' => '+1234567890',
                'role' => 'admin',
                'type' => 'admin',
            ]
        );

        $coach = User::firstOrCreate(
            ['email' => 'coach@verve.com'],
            [
                'name' => 'John Coach',
                'email' => 'coach@verve.com',
                'password' => bcrypt('password'),
                'phone' => '+1234567891',
                'role' => 'user',
                'type' => 'coach',
                'bio' => 'Experienced fitness coach',
                'specialization' => 'Strength Training',
            ]
        );

        $client = User::firstOrCreate(
            ['email' => 'client@verve.com'],
            [
                'name' => 'Jane Client',
                'email' => 'client@verve.com',
                'password' => bcrypt('password'),
                'phone' => '+1234567892',
                'role' => 'user',
                'type' => 'client',
            ]
        );

        // Get all users for replies
        $users = User::whereIn('id', [$admin->id, $coach->id, $client->id])->get();
        
        // If we have other users, include them too
        $allUsers = User::limit(10)->get();
        if ($allUsers->count() > 3) {
            $users = $allUsers;
        }

        // Create Categories
        $categories = [
            [
                'name' => 'General Discussion',
                'slug' => 'general-discussion',
                'description' => 'General fitness discussions, questions, and community chat',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Training Tips & Techniques',
                'slug' => 'training-tips',
                'description' => 'Share and learn about workout techniques, training methods, and exercise tips',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Nutrition & Diet',
                'slug' => 'nutrition-diet',
                'description' => 'Discuss nutrition, meal plans, supplements, and healthy eating habits',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Success Stories',
                'slug' => 'success-stories',
                'description' => 'Share your fitness journey, transformations, and achievements',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Equipment & Gear',
                'slug' => 'equipment-gear',
                'description' => 'Discuss fitness equipment, gear reviews, and recommendations',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Motivation & Support',
                'slug' => 'motivation-support',
                'description' => 'Get motivated, share challenges, and support each other on your fitness journey',
                'order' => 6,
                'is_active' => true,
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $catData) {
            $category = Category::firstOrCreate(
                ['slug' => $catData['slug']],
                $catData
            );
            $createdCategories[] = $category;
        }

        // Topics and Posts data
        $topicsData = [
            // General Discussion
            [
                'category' => 'general-discussion',
                'title' => 'Welcome to Verve Fitness Community!',
                'body' => 'Welcome everyone to the Verve Fitness Community! This is a place where we can share our fitness journeys, ask questions, and support each other. Feel free to introduce yourself and let\'s build a strong, supportive community together!',
                'user' => $admin,
                'is_pinned' => true,
                'replies' => [
                    'Great to be here! Looking forward to connecting with everyone.',
                    'Thanks for creating this community. Excited to learn and share!',
                    'Hello everyone! Can\'t wait to start my fitness journey with you all.',
                ],
            ],
            [
                'category' => 'general-discussion',
                'title' => 'What are your fitness goals for this month?',
                'body' => 'Let\'s share our fitness goals for this month! Whether it\'s losing weight, building muscle, improving flexibility, or training for an event, let\'s motivate each other to achieve our goals.',
                'user' => $client,
                'replies' => [
                    'My goal is to lose 5 pounds and run 3 times a week. What about you?',
                    'I want to build more upper body strength. Starting a new program this week!',
                ],
            ],
            [
                'category' => 'general-discussion',
                'title' => 'Best time of day to workout?',
                'body' => 'I\'ve been trying to figure out the best time to workout. Some say morning is best, others prefer evening. What works best for you and why?',
                'user' => $client,
                'replies' => [
                    'I prefer morning workouts - gives me energy for the whole day!',
                    'Evening works better for me. I feel stronger and more focused after work.',
                    'As a coach, I recommend whatever time you can be consistent with. Consistency beats timing!',
                ],
            ],

            // Training Tips
            [
                'category' => 'training-tips',
                'title' => 'Proper Form: The Foundation of Effective Training',
                'body' => 'Proper form is crucial for preventing injuries and maximizing results. Let\'s discuss the importance of form and share tips on maintaining correct posture during exercises. What are your go-to form cues?',
                'user' => $coach,
                'is_pinned' => true,
                'replies' => [
                    'Great topic! I always remind my clients: form over weight. Better to lift lighter with perfect form than heavy with bad form.',
                    'Video recording yourself is a game-changer for checking form. Highly recommend!',
                    'Core engagement is key for almost every exercise. Don\'t forget to brace!',
                ],
            ],
            [
                'category' => 'training-tips',
                'title' => 'How to Progress in Your Workouts Safely',
                'body' => 'Progressive overload is essential for continued improvement, but it needs to be done safely. How do you gradually increase the difficulty of your workouts? Share your strategies!',
                'user' => $coach,
                'replies' => [
                    'I increase weight by 5-10% when I can complete all sets with perfect form.',
                    'Adding one more rep or set before increasing weight works well for me.',
                    'Don\'t forget about rest days! Recovery is part of progression.',
                ],
            ],
            [
                'category' => 'training-tips',
                'title' => 'Home Workout Equipment Recommendations',
                'body' => 'For those working out at home, what equipment do you find most valuable? Looking for recommendations on what to invest in for a home gym setup.',
                'user' => $client,
                'replies' => [
                    'Resistance bands are versatile and affordable. Great for beginners!',
                    'A good set of dumbbells and a bench can take you far.',
                    'Don\'t underestimate bodyweight exercises. You can do a lot with just your body!',
                ],
            ],

            // Nutrition
            [
                'category' => 'nutrition-diet',
                'title' => 'Pre and Post Workout Nutrition Tips',
                'body' => 'What you eat before and after workouts can significantly impact your performance and recovery. Let\'s share our favorite pre and post-workout meals and snacks!',
                'user' => $coach,
                'replies' => [
                    'I like a banana and some coffee before morning workouts. Simple and effective!',
                    'Post-workout: protein shake with some carbs within 30 minutes works best for me.',
                    'Don\'t overthink it. Focus on whole foods and proper hydration.',
                ],
            ],
            [
                'category' => 'nutrition-diet',
                'title' => 'Meal Prep Ideas for Busy Schedules',
                'body' => 'Meal prep has been a game-changer for me. Share your favorite meal prep recipes and tips for staying on track with nutrition during busy weeks!',
                'user' => $client,
                'replies' => [
                    'I prep chicken, rice, and veggies on Sundays. Makes the week so much easier!',
                    'Overnight oats for breakfast - quick, healthy, and delicious!',
                    'Invest in good containers. Makes a huge difference!',
                ],
            ],
            [
                'category' => 'nutrition-diet',
                'title' => 'How to Stay Hydrated During Workouts',
                'body' => 'Hydration is often overlooked but crucial for performance. How much water do you drink during workouts? Any tips for staying properly hydrated?',
                'user' => $client,
                'replies' => [
                    'I aim for 500ml per hour of exercise. More if it\'s hot or intense.',
                    'Electrolytes are important for longer sessions. Consider adding them!',
                    'Start hydrated - drink water throughout the day, not just during workouts.',
                ],
            ],

            // Success Stories
            [
                'category' => 'success-stories',
                'title' => 'My 6-Month Transformation Journey',
                'body' => 'I wanted to share my journey over the past 6 months. Started at 200lbs, now down to 175lbs through consistent training and better nutrition. The key was finding a routine I could stick to! Here\'s what worked for me...',
                'user' => $client,
                'replies' => [
                    'Amazing progress! You should be so proud. Keep it up!',
                    'This is so inspiring! Can you share more about your routine?',
                    'Congratulations! Your dedication really shows.',
                ],
            ],
            [
                'category' => 'success-stories',
                'title' => 'Finally Hit My Deadlift PR!',
                'body' => 'After months of training, I finally hit a 300lb deadlift! It\'s been a goal of mine for a while. Consistency and proper programming made all the difference.',
                'user' => $client,
                'replies' => [
                    'That\'s incredible! Well done!',
                    'What was your training program like?',
                    'Congratulations on the PR! Keep pushing!',
                ],
            ],

            // Equipment & Gear
            [
                'category' => 'equipment-gear',
                'title' => 'Best Running Shoes for Different Terrain',
                'body' => 'Looking for recommendations on running shoes. I run both on trails and roads. What shoes have worked best for you?',
                'user' => $client,
                'replies' => [
                    'I love my trail runners for both. Good grip and cushioning.',
                    'Get fitted at a running store. Proper fit is more important than brand.',
                    'I rotate between two pairs - helps them last longer and prevents injuries.',
                ],
            ],
            [
                'category' => 'equipment-gear',
                'title' => 'Gym Bag Essentials - What Do You Carry?',
                'body' => 'What are your must-have items in your gym bag? Looking to optimize what I bring to the gym.',
                'user' => $client,
                'replies' => [
                    'Water bottle, towel, resistance bands, and a good lock!',
                    'Don\'t forget deodorant and a change of clothes.',
                    'I always bring a notebook to track my workouts.',
                ],
            ],

            // Motivation & Support
            [
                'category' => 'motivation-support',
                'title' => 'Struggling with Motivation - Need Encouragement',
                'body' => 'I\'ve been struggling to stay motivated lately. Life has been busy and I\'ve been skipping workouts. Any advice on getting back on track?',
                'user' => $client,
                'replies' => [
                    'You\'re not alone! Start small - even 15 minutes is better than nothing.',
                    'Remember why you started. Write down your goals and keep them visible.',
                    'Find an accountability partner. It makes a huge difference!',
                    'Don\'t be too hard on yourself. Progress isn\'t linear. Just get back to it!',
                ],
            ],
            [
                'category' => 'motivation-support',
                'title' => 'Weekly Motivation Thread - Share Your Wins!',
                'body' => 'Let\'s share our wins from this week, no matter how small! Celebrating progress keeps us motivated. What did you accomplish this week?',
                'user' => $coach,
                'replies' => [
                    'I finally did 10 push-ups in a row! Small but huge for me!',
                    'Stuck to my meal plan all week. Feeling great!',
                    'Completed all my scheduled workouts. Consistency win!',
                ],
            ],
        ];

        // Create topics and posts
        foreach ($topicsData as $topicData) {
            $category = collect($createdCategories)->firstWhere('slug', $topicData['category']);
            if (!$category || !$topicData['user']) {
                continue;
            }

            // Create topic
            $topic = Topic::create([
                'category_id' => $category->id,
                'user_id' => $topicData['user']->id,
                'title' => $topicData['title'],
                'slug' => Str::slug($topicData['title']) . '-' . uniqid(),
                'body' => $topicData['body'],
                'is_pinned' => $topicData['is_pinned'] ?? false,
                'is_locked' => false,
                'views_count' => rand(10, 150),
                'replies_count' => count($topicData['replies'] ?? []),
                'last_reply_at' => now()->subDays(rand(0, 7)),
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            // Create first post (the topic body)
            Post::create([
                'topic_id' => $topic->id,
                'user_id' => $topicData['user']->id,
                'body' => $topicData['body'],
                'is_first_post' => true,
                'created_at' => $topic->created_at,
            ]);

            // Create reply posts
            if (isset($topicData['replies']) && count($topicData['replies']) > 0) {
                $replyUsers = $users->shuffle();
                $replyIndex = 0;
                
                foreach ($topicData['replies'] as $replyBody) {
                    $replyUser = $replyUsers[$replyIndex % $replyUsers->count()];
                    
                    Post::create([
                        'topic_id' => $topic->id,
                        'user_id' => $replyUser->id,
                        'body' => $replyBody,
                        'is_first_post' => false,
                        'created_at' => $topic->created_at->addHours(rand(1, 48)),
                    ]);
                    
                    $replyIndex++;
                }
            }

            // Update topic's last_reply_at based on latest post
            $latestPost = $topic->posts()->latest()->first();
            if ($latestPost) {
                $topic->update(['last_reply_at' => $latestPost->created_at]);
            }
        }

        $this->command->info('Forum seeded successfully!');
        $this->command->info('Created ' . count($createdCategories) . ' categories');
        $this->command->info('Created ' . count($topicsData) . ' topics with posts');
    }
}

