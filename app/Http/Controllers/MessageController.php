<?php

// phpcs:ignoreFile

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * Create a new controller instance and apply auth middleware
     */
    public function __construct()
    {
        $this->middleware('auth'); // Requires authentication for all methods
    }

    /**
     * Get all messages with their read status
     */

    public function index(Request $request)
    {
        try {
            $messages = Message::all(); // Or use Message::paginate(10) for pagination
            return response()->json([
                'messages' => $messages,
                'message' => 'Messages retrieved successfully',
            ], 200);
        } catch (\Exception $e) {

            Log::error('Error retrieving messages: ' . $e->getMessage());

            return response()->json(['message' => 'Failed to retrieve messages', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Toggle the is_read status of a message
     */
    public function toggleReadStatus(Request $request, $id)
    {
        try {
            $message = Message::findOrFail($id); // Find the message by ID

            // Toggle the is_read status
            $message->isRead = !$message->isRead;
            $message->save();


            Log::info('Message read status updated: ', [
                'id' => $message->id,
                'isRead' => $message->isRead,
            ]);

            return response()->json([

                'message' => 'Read status updated successfully',
                'isRead' => $message->isRead,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating read status: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update read status', 'error' => $e->getMessage()], 500);
        }
    }
}
