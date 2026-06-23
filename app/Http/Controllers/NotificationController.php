<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orWhereNull('user_id')
            ->latest()
            ->take(20)
            ->get();

        return response()->json($notifications);
    }

    public function markRead($id)
    {
        Notification::where('id', $id)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->orWhereNull('user_id')
            ->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        $count = Notification::where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhereNull('user_id');
            })
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}   