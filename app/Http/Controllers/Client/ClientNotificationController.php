<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientNotificationController extends Controller
{
    /**
     * Display a listing of client notifications.
     */
    public function index(Request $request): View
    {
        $query = $request->user()->notifications();

        if ($request->boolean('unread')) {
            $query = $request->user()->unreadNotifications();
        }

        if ($request->filled('category')) {
            $category = $request->input('category');
            $query->where('data->category', $category);
        }

        $notifications = $query->paginate(15)->withQueryString();

        return view('client.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->firstOrFail();

        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark a specific notification as unread.
     */
    public function markAsUnread(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->firstOrFail();

        $notification->update(['read_at' => null]);

        return back()->with('success', 'Notification marked as unread.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
