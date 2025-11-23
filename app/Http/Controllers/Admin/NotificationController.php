<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;

class NotificationController extends Controller
{
    protected function ensureCanApprove()
    {
        $user = auth()->user();
        if (!($user && $user instanceof User && method_exists($user,'hasPermission') && $user->hasPermission('income.approve'))) {
            abort(403);
        }
    }

    public function index()
    {
        $this->ensureCanApprove();
        $user = auth()->user();
        $notifications = DatabaseNotification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markRead($id)
    {
        $this->ensureCanApprove();
        $user = auth()->user();
        $notification = DatabaseNotification::where('id', $id)
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->firstOrFail();
        if ($notification->read_at === null) {
            $notification->read_at = now();
            $notification->save();
        }
        return back()->with('success','Notifikasi ditandai telah dibaca.');
    }

    public function toggle($id)
    {
        $this->ensureCanApprove();
        $user = auth()->user();
        $notification = DatabaseNotification::where('id', $id)
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->firstOrFail();
        if ($notification->read_at) {
            $notification->read_at = null; // mark as unread
            $notification->save();
            return back()->with('success','Notifikasi ditandai belum dibaca.');
        } else {
            $notification->read_at = now();
            $notification->save();
            return back()->with('success','Notifikasi ditandai telah dibaca.');
        }
    }
}
