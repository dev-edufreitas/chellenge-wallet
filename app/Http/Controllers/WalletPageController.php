<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WalletPageController extends Controller
{
    public function dashboard(Request $request)
    {
        $balance      = $request->user()->wallet->balance;
        $transactions = $request->user()->wallet->transactions()->latest()->paginate(5);

        return view('wallet.dashboard', compact('balance', 'transactions'));
    }

    public function depositForm()
    {
        return view('wallet.deposit');
    }

    public function transferForm()
    {
        return view('wallet.transfer');
    }

    
}
