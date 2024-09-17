<?php

// app/Http/Controllers/BlogController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();
        return view('profile.notifications', [
            'title' => 'Сповіщення',
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();
        return 'success';
    }

    public function markAllAsRead()
    {
        $user = auth()->user();
        foreach ($user->notifications as $notification) {
            $notification->markAsRead();
        }

        return redirect()->back();
    }


    
}

