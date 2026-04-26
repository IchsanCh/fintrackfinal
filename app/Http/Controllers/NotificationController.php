<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementRead;
use App\Models\Budget;
use App\Models\SavingGoal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    // GET /notifications
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $tab    = $request->input('tab', 'notifications');
        $type   = $request->input('type', 'all');

        // Notifikasi personal
        $notifQuery = $user->notifications()->latest();

        if ($type !== 'all') {
            $notifQuery->where('type', $type);
        }

        $notifications = $notifQuery
            ->paginate(20, ['*'], 'notif_page')
            ->withQueryString();

        $unreadNotifCount = $user->notifications()->where('is_read', false)->count();

        // Pengumuman aktif
        $readIds = AnnouncementRead::where('user_id', $user->id)->pluck('announcement_id');

        $announcements = Announcement::where('is_active', true)
            ->latest()
            ->paginate(20, ['*'], 'ann_page')
            ->withQueryString();

        $unreadAnnCount = Announcement::where('is_active', true)
            ->whereNotIn('id', $readIds)
            ->count();

        return view('user.notifications.index', compact(
            'notifications',
            'announcements',
            'tab',
            'type',
            'unreadNotifCount',
            'unreadAnnCount',
            'readIds'
        ));
    }

    // PATCH /notifications/{notification}/read
    public function markRead(\App\Models\Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        // Redirect berdasarkan tipe
        $redirect = match ($notification->type) {
            'budget_warning' => route('budgets.index'),
            'savings_goal'   => route('saving-goals.index'),
            'bill_reminder'  => route('bill-reminders.index'),
            default          => route('notifications.index'),
        };

        return redirect($redirect);
    }

    // POST /notifications/mark-all-read
    public function markAllRead(): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->notifications()->where('is_read', false)->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    // DELETE /notifications/{notification}
    public function destroy(\App\Models\Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notifikasi dihapus.');
    }

    // POST /notifications/delete-all
    public function destroyAll(): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->notifications()->delete();

        return back()->with('success', 'Semua notifikasi dihapus.');
    }

    // GET /announcements/{announcement}
    public function showAnnouncement(Announcement $announcement): View
    {
        if (! $announcement->is_active) {
            abort(404);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Mark as read
        AnnouncementRead::firstOrCreate(
            ['user_id' => $user->id, 'announcement_id' => $announcement->id],
            ['read_at' => now()]
        );

        return view('user.notifications.announcement-detail', compact('announcement'));
    }
}
