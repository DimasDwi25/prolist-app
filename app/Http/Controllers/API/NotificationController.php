<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Get unread notifications (for polling)
    public function index(Request $request)
    {
        return response()->json([
            'notifications' => $request->user()->unreadNotifications
        ]);
    }

    // Get all notifications (read and unread)
    public function all(Request $request)
    {
        return response()->json([
            'notifications' => $request->user()->notifications
        ]);
    }

    // Get count of unread notifications
    public function count(Request $request)
    {
        return response()->json([
            'unread_count' => $request->user()->unreadNotifications->count()
        ]);
    }

    // Mark specific notification as read (delete it)
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()
            ->unreadNotifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->delete();
            return response()->json(['message' => 'Notification deleted']);
        }

        return response()->json(['message' => 'Notification not found or already read'], 404);
    }

    // Mark all notifications as read (delete them)
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications()->delete();

        return response()->json(['message' => 'All notifications deleted']);
    }

    // Delete a specific notification
    public function destroy(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->delete();
            return response()->json(['message' => 'Notification deleted']);
        }

        return response()->json(['message' => 'Notification not found'], 404);
    }
}
