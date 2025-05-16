<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Exibe a dashboard do usuário com o saldo e transações
     */
    public function dashboard(Request $request)
    {
        $wallet = $request->user()->wallet;
        $balance = $wallet->balance;
        $transactions = $wallet->transactions()->latest()->paginate(5);

        return view('wallet.dashboard', compact('balance', 'transactions', 'wallet'));
    }
    /**
     * Exibe o formulário de depósito
     */
    public function showDepositForm()
    {
        return view('wallet.deposit');
    }

    /**
     * Exibe o formulário de transferência
     */
    public function showTransferForm()
    {
        return view('wallet.transfer');
    }

    /**
     * Processa um depósito
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $this->walletService->deposit($request->user(), $request->amount, $request->description);
            return redirect()->route('dashboard')->with('success', 'Depósito efetuado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Processa a transferência
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'to_user_email' => 'required|email|exists:users,email',
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $toUser = User::where('email', $request->to_user_email)->firstOrFail();
            $this->walletService->transfer($request->user(), $toUser, $request->amount);

            return redirect()->route('dashboard')->with('success', 'Transferência realizada com sucesso!');
        } catch (\Exception $e) {
            // Redireciona de volta com o erro
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Reverte uma transação
     */
    public function revertTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_reference' => 'required|string|uuid',
            'reversal_reason'       => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $this->walletService->revertTransaction(
                $request->transaction_reference,
                $request->reversal_reason ?? 'Solicitação do usuário'
            );

            return redirect()->back()->with('success', 'Transação revertida com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
