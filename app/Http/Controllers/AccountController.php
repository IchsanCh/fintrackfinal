<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\TierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    // GET /accounts
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $accounts     = $user->accounts()->latest()->get();
        $totalBalance = $accounts->sum('balance');
        $tierSummary  = TierService::getSummary($user);

        return view('user.accounts.index', compact('accounts', 'totalBalance', 'tierSummary'));
    }

    // POST /accounts
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Cek limit tier
        if (! TierService::canAddAccount($user)) {
            $plan = TierService::getActivePlan($user);
            return back()->withErrors([
                'limit' => "Paket {$plan->name} hanya bisa memiliki maksimal {$plan->max_accounts} akun. Upgrade untuk menambah akun.",
            ]);
        }

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'type'    => ['required', 'in:cash,bank,e-wallet'],
            'balance' => ['required', 'numeric', 'min:0'],
        ]);

        $user->accounts()->create($data);

        return back()->with('success', 'Akun berhasil ditambahkan.');
    }

    // PUT /accounts/{account}
    public function update(Request $request, Account $account): RedirectResponse
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'type'    => ['required', 'in:cash,bank,e-wallet'],
            'balance' => ['required', 'numeric', 'min:0'],
        ]);

        $account->update($data);

        return back()->with('success', 'Akun berhasil diperbarui.');
    }

    // DELETE /accounts/{account}
    public function destroy(Account $account): RedirectResponse
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        if ($account->transactions()->exists()) {
            return back()->withErrors(['delete' => 'Akun tidak bisa dihapus karena masih memiliki transaksi.']);
        }

        $account->delete();

        return back()->with('success', 'Akun berhasil dihapus.');
    }
}
