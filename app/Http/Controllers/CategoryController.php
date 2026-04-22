<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Default kategori untuk user baru.
     */
    public const DEFAULT_CATEGORIES = [
        ['name' => 'Gaji',       'type' => 'income',  'icon' => 'banknotes'],
        ['name' => 'Freelance',  'type' => 'income',  'icon' => 'computer-desktop'],
        ['name' => 'Investasi',  'type' => 'income',  'icon' => 'arrow-trending-up'],
        ['name' => 'Bonus',      'type' => 'income',  'icon' => 'gift'],
        ['name' => 'Makan',      'type' => 'expense', 'icon' => 'fire'],
        ['name' => 'Transport',  'type' => 'expense', 'icon' => 'truck'],
        ['name' => 'Hiburan',    'type' => 'expense', 'icon' => 'film'],
        ['name' => 'Belanja',    'type' => 'expense', 'icon' => 'shopping-cart'],
        ['name' => 'Tagihan',    'type' => 'expense', 'icon' => 'document-text'],
        ['name' => 'Kesehatan',  'type' => 'expense', 'icon' => 'heart'],
        ['name' => 'Pendidikan', 'type' => 'expense', 'icon' => 'academic-cap'],
        ['name' => 'Lainnya',    'type' => 'expense', 'icon' => 'cube'],
    ];

    /**
     * Generate default kategori untuk user.
     */
    public static function seedDefaults(int $userId): void
    {
        foreach (self::DEFAULT_CATEGORIES as $cat) {
            Category::create([...$cat, 'user_id' => $userId]);
        }
    }

    // GET /categories
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $categories = $user->categories()->orderBy('type')->orderBy('name')->get();
        $incomes    = $categories->where('type', 'income');
        $expenses   = $categories->where('type', 'expense');

        return view('user.categories.index', compact('categories', 'incomes', 'expenses'));
    }

    // POST /categories
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'type' => ['required', 'in:income,expense'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->categories()->create($data);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    // PUT /categories/{category}
    public function update(Request $request, Category $category): RedirectResponse
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'type' => ['required', 'in:income,expense'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $category->update($data);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    // DELETE /categories/{category}
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        if ($category->transactions()->exists()) {
            return back()->withErrors(['delete' => 'Kategori tidak bisa dihapus karena masih digunakan oleh transaksi.']);
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
