<?php

namespace App\Http\Controllers;

use App\Models\WebUser;
use Illuminate\Http\Request;
use App\Mail\SendEmail;
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
            'email' => 'required|email'
        ]);

        // Store the user contacting us
        try {
            $webUser = WebUser::firstOrCreate(
                ['email' => $validated['email']],
                ['name' => $validated['name']]
            );

            Log::info('New web user stored: ', ['name' => $webUser->name, 'email' => $webUser->email]);
        } catch (\Exception $e) {
            Log::error('Error storing web user: ' . $e->getMessage());
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
}
