<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Models\User;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $this->walletService->deposit($request->user(), $request->amount, $request->description);

        return redirect()->route('dashboard')->with('success', 'Depósito efetuado com sucesso!');
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'to_user_email' => 'required|email|exists:users,email',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $toUser = User::where('email', $request->to_user_email)->firstOrFail();
        $this->walletService->transfer($request->user(), $toUser, $request->amount);

        return redirect()->route('dashboard')->with('success', 'Transferência realizada com sucesso!');
    }
    

    public function revertTransaction(Request $request)
    {
        $this->walletService->revertTransaction($request->get('transaction_reference'));
        return redirect()->back()->with('success', 'Transação revertida com sucesso.');
    }
    

    public function balance(Request $request)
    {
        $balance = $request->user()->wallet->balance;

        return view('wallet.balance', compact('balance'));
    }
}
