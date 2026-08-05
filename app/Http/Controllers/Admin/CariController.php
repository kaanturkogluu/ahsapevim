<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CariController extends Controller
{
    // List customers with balances
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = User::where('is_admin', false);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15);
        
        $totalBalance = User::where('is_admin', false)->sum('balance');
        $totalDebt = User::where('is_admin', false)->where('balance', '>', 0)->sum('balance');
        $totalCredit = User::where('is_admin', false)->where('balance', '<', 0)->sum('balance');

        return view('admin.cari.index', compact('users', 'totalBalance', 'totalDebt', 'totalCredit', 'search'));
    }

    // Show customer's statement (ekstre)
    public function show(User $user)
    {
        if ($user->is_admin) {
            abort(403, 'Yöneticilerin cari hesabı olmaz.');
        }

        $transactions = $user->transactions()->orderBy('date', 'desc')->paginate(30);

        return view('admin.cari.show', compact('user', 'transactions'));
    }

    // Add manual transaction
    public function store(Request $request, User $user)
    {
        if ($user->is_admin) {
            abort(403, 'Yöneticilere işlem eklenemez.');
        }

        $validated = $request->validate([
            'type' => 'required|in:debit,credit',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'date' => 'nullable|date'
        ]);

        $amount = $validated['amount'];
        $type = $validated['type'];
        
        DB::transaction(function () use ($user, $validated, $amount, $type) {
            $user->transactions()->create([
                'type' => $type,
                'amount' => $amount,
                'description' => $validated['description'],
                'date' => $validated['date'] ?? now(),
            ]);

            // Update balance
            // debit (borç) increases balance. credit (alacak/ödeme) decreases balance.
            if ($type === 'debit') {
                $user->balance += $amount;
            } else {
                $user->balance -= $amount;
            }
            $user->save();
        });

        return redirect()->route('admin.cari.show', $user->id)
                         ->with('success', 'İşlem başarıyla eklendi ve bakiye güncellendi.');
    }
}
