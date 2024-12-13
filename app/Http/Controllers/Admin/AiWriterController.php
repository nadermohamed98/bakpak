<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\SettingRepository;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class AiWriterController extends Controller
{
    public function useCases(): array
    {
        return [
            'product_description'  => 'Product Description',
            'brand_name'           => 'Brand Name',
            'email'                => 'Email',
            'email_reply'          => 'Email Reply',
            'review_feedback'      => 'Review Feedback',
            'blog_idea'            => 'Blog Idea & Outline',
            'blog_writing'         => 'Blog Section Writing',
            'business_idea'        => 'Business Ideas',
            'business_idea_pitch'  => 'Business Idea Pitch',
            'proposal_later'       => 'Proposal Later',
            'cover_letter'         => 'Cover Letter',
            'call-to_action'       => 'Call to Action',
            'job_description'      => 'Job Description',
            'legal_agreement'      => 'Legal Agreement',
            'social_ads'           => 'Facebook, Twitter, Linkedin Ads',
            'google_ads'           => 'Google Search Ads',
            'post_idea'            => 'Post & Caption Ideas',
            'police_general_dairy' => 'Police General Dairy',
            'comment_reply'        => 'Comment Reply',
            'birthday_wish'        => 'Birthday Wish',
            'seo_meta'             => 'SEO Meta Description',
            'seo_title'            => 'SEO Meta Title',
            'song_lyrics'          => 'Song Lyrics',
            'story_plot'           => 'Story Plot',
            'review'               => 'Review',
            'testimonial'          => 'Testimonial',
            'video_des'            => 'Video Description',
            'video_idea'           => 'Video Idea',
            'php_code'             => 'PHP Code',
            'python_code'          => 'Python Code',
            'java_code'            => 'Java Code',
            'javascript_code'      => 'Javascript Code',
            'dart_code'            => 'Dart Code',
            'swift_code'           => 'Swift Code',
            'c_code'               => 'C Code',
            'c#_code'              => 'C# Code',
            'mysql_query'          => 'MySQL Query',
            'about_us'             => 'About Us',
        ];
    }

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $data = [
            'use_cases' => $this->useCases(),
        ];

        return view('backend.admin.ai_writer.index', $data);
    }

    public function saveAiSetting(Request $request, SettingRepository $setting): \Illuminate\Http\JsonResponse
    {
        if (config('app.demo_mode')) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        $request->validate([
            'ai_secret_key' => 'required',
        ]);

        try {
            $setting->update($request);
            Toastr::success(__('update_successful'));
            $data = [
                'success' => __('update_successful'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'error' => $e->getMessage(),
            ];

            return response()->json($data);
        }
    }
    
    public function generateContent(Request $request)
    {
        $request->validate([
            'prompt' => 'required',
            'length' => 'required',
        ]);

        try {

            $api_key = setting('ai_secret_key');
        
            if (!$api_key) {
              return response()->json(['error' => 'API key is missing in settings.' .$api_key]);
            }

            $url = 'https://api.openai.com/v1/chat/completions';
            $headers = [
                'Content-type'  => 'application/json',
                'Authorization' => "Bearer $api_key",
            ];

            echo $request->prompt;

            $messages = [
                ['role' => 'user', 'content' => $request->prompt]
            ];

            $body = [
                'model' => 'gpt-4', // Make sure to use the appropriate model
                'messages' => $messages,
                'max_tokens' => (int) $request->length,
                'temperature' => 1,
                // 'n' => (int) $request->variants, // Usually not needed for chat unless multiple completions are desired
            ];
            $result = curlRequest($url, json_encode($body), 'POST', $headers, true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        try {

            if (is_string($request)) {
                $result = json_decode($request, true);
            }

            if (!is_array($result) || !isset($result['choices'])) {
                return response()->json(['error' => 'Invalid API response']);
            }

            // Check if 'choices' exists and has at least one item
            if (!(arrayCheck('choices', $result) && count($result['choices']) > 0)) {
                return response()->json([
                    'error' => 'No choices available. Please try again.',
                ]);
            }
        
            // Access the 'message' array in the first choice if available
            $messages = $result['choices'][0]['message'] ?? null; // Assuming the response structure is understood and consistent
        
            if (!$messages || !arrayCheck('content', $messages)) {
                return response()->json([
                    'error' => 'Missing message content.',
                ]);
            }
        
            // Replace newlines with HTML breaks if necessary
            $data = str_replace("\n", '<br>', $messages['content']);
        
            return response()->json([
                'content' => $data,
                'success' => 1,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }
}
