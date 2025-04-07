<?php

// phpcs:ignoreFile

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\WebUser;
use Illuminate\Http\Request;
use App\Mail\SendEmail;
use App\Mail\SendNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController
{
    public function sendEmail(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'name' => 'required|string',
            'message' => 'required|string',
            'email' => 'required|email',
            'isNotified' => 'nullable|boolean'
        ]);

        try {
            $message = Message::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'message' => $validated['message'],
            ]);

            Log::info('New message stored: ', ['id' => $message->id, 'email' => $message->email]);
        } catch (\Exception $e) {
            Log::error('Error storing message: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to store message', 'error' => $e->getMessage()], 500);
        }

        // Store or update the user in web_users table
        try {
            $webUser = WebUser::updateOrCreate(
                ['email' => $validated['email']], // Match on email
                [
                    'name' => $validated['name'], // Update name if changed
                    'isNotified' => $request->input('isNotified', false), // Update notification preference, default to false
                ]
            );

            Log::info('Web user stored/updated: ', [
                'name' => $webUser->name,
                'email' => $webUser->email,
                'wants_notifications' => $webUser->wants_notifications,
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing/updating web user: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to store user', 'error' => $e->getMessage()], 500);
        }
        // Prepare email data
        $data = [
            'name' => $validated['name'],
            'message' => $validated['message'],
            'sender_email' => $validated['email'],
            'sender_name' => $validated['name'],
        ];

        try {
            // Send email to dev email, using user's email as the reply-to
            Mail::to('dev@smrheavymaq.com')->send(new SendEmail($data));

            // Return success response
            return response()->json(['message' => 'Email sent successfully!'], 200);
        } catch (\Exception $e) {
            // Log error and return failure response
            Log::error('Email sending failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send email', 'error' => $e->getMessage()], 500);
        }
    }

    public function sendNotifications(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $users = WebUser::where('isNotified', true)->get();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users opted in for notifications'], 200);
        }

        foreach ($users as $user) {
            try {
                $notificationData = [
                    'name' => $user->name,
                    'message' => $validated['message'],
                ];
                Mail::to($user->email)->send(new SendNotification($notificationData));
                Log::info('Notification sent to: ', ['email' => $user->email]);
            } catch (\Exception $e) {
                Log::error('Failed to send notification to ' . $user->email . ': ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Notifications sent successfully!'], 200);
    }

}
