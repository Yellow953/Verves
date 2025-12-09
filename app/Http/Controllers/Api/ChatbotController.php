<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle chatbot message and return AI response.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_history' => 'nullable|array',
        ]);

        // Check if user is authenticated (optional)
        $user = null;
        try {
            // Try to get authenticated user (works with Sanctum)
            $user = $request->user();
        } catch (\Exception $e) {
            // Not authenticated, continue as guest
            $user = null;
        }
        
        $userType = $user ? ($user->type === 'coach' ? 'coach' : ($user->type === 'client' ? 'client' : 'visitor')) : 'visitor';
        $userName = $user ? $user->name : 'Guest';

        // Build system prompt based on user type
        $systemPrompt = $this->buildSystemPrompt($userType);

        // Build conversation messages
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Add conversation history if provided
        if (isset($validated['conversation_history']) && is_array($validated['conversation_history'])) {
            foreach ($validated['conversation_history'] as $history) {
                if (isset($history['role']) && isset($history['content'])) {
                    $messages[] = [
                        'role' => $history['role'],
                        'content' => $history['content'],
                    ];
                }
            }
        }

        // Add current user message
        $messages[] = [
            'role' => 'user',
            'content' => $validated['message'],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiMessage = $data['choices'][0]['message']['content'] ?? 'I apologize, but I could not generate a response. Please try again.';

                return response()->json([
                    'success' => true,
                    'data' => [
                        'message' => trim($aiMessage),
                    ],
                ]);
            } else {
                Log::error('OpenAI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, I encountered an error. Please try again later.',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Chatbot Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sorry, I encountered an error. Please try again later.',
            ], 500);
        }
    }

    /**
     * Build system prompt based on user type.
     */
    private function buildSystemPrompt(string $userType): string
    {
        $basePrompt = "You are a helpful and friendly AI assistant for Verve Fitness, a fitness coaching platform. ";

        if ($userType === 'coach') {
            return $basePrompt . "You are assisting a fitness coach. Help them with:
- Managing clients and training programs
- Exercise recommendations and program design
- Booking and scheduling questions
- Platform features and navigation
- General fitness coaching advice

Be professional, concise, and helpful. If you don't know something specific about the platform, suggest they check the dashboard or contact support.";
        } elseif ($userType === 'client') {
            return $basePrompt . "You are assisting a fitness client. Help them with:
- Finding and booking coaches
- Understanding training programs
- Using platform features
- General fitness questions
- Account and subscription inquiries

Be friendly, encouraging, and supportive. If you don't know something specific about the platform, suggest they check their dashboard or contact their coach.";
        } else {
            return $basePrompt . "You are assisting a visitor. Help them with:
- Learning about Verve Fitness platform
- Understanding services and features
- General fitness questions
- How to get started

Be welcoming and informative. Encourage them to sign up to access more features.";
        }
    }
}

