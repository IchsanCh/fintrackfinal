<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    // GET /admin/announcements
    public function index(): View
    {
        $announcements = Announcement::with('admin')
            ->latest()
            ->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    // GET /admin/announcements/create
    public function create(): View
    {
        return view('admin.announcements.form', [
            'announcement' => null,
        ]);
    }

    // POST /admin/announcements
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'     => ['required', 'string', 'max:255'],
            'content'   => ['required', 'string'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['admin_id']  = Auth::id();

        Announcement::create($data);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    // GET /admin/announcements/{announcement}/edit
    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.form', compact('announcement'));
    }

    // PUT /admin/announcements/{announcement}
    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $data = $request->validate([
            'title'     => ['required', 'string', 'max:255'],
            'content'   => ['required', 'string'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->has('is_active');

        $announcement->update($data);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    // PATCH /admin/announcements/{announcement}/toggle
    public function toggle(Announcement $announcement): RedirectResponse
    {
        $announcement->update(['is_active' => ! $announcement->is_active]);

        $status = $announcement->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Pengumuman berhasil {$status}.");
    }

    // DELETE /admin/announcements/{announcement}
    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
