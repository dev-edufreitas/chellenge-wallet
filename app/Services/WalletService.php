<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Str;

class WalletService
{
    public function deposit(User $user, float $amount,  $description = null): void
    {
        $wallet = $user->wallet;

        // Ajusta o valor se o saldo for negativo
        if ($wallet->balance < 0) {
            $amount += abs($wallet->balance);
        }

        // Atualiza o saldo
        $wallet->balance += $amount;
        $wallet->save();

        // Cria o registro da transação
        $wallet->transactions()->create([
            'type' => 'deposit',
            'amount' => $amount,
            'description' => $description,
            'transaction_reference' => Str::uuid(),
        ]);
    }

    public function transfer(User $fromUser, User $toUser, float $amount): void
    {
        // Verifica saldo
        if ($fromUser->wallet->balance < $amount) {
            throw new \Exception('Saldo insuficiente para realizar a transferência.');
        }

        $transactionReference = Str::uuid();

        // Desconta do remetente
        $fromUser->wallet->balance -= $amount;
        $fromUser->wallet->save();
        $fromUser->wallet->transactions()->create([
            'type' => 'transfer_out',
            'amount' => -$amount,
            'description' => "Transferência para {$toUser->email}",
            'transaction_reference' => $transactionReference,
        ]);

        // Adiciona ao destinatário
        $toUser->wallet->balance += $amount;
        $toUser->wallet->save();
        $toUser->wallet->transactions()->create([
            'type' => 'transfer_in',
            'amount' => $amount,
            'description' => "Recebido de {$fromUser->email}",
            'transaction_reference' => $transactionReference,
        ]);
    }

    public function revertTransaction(string $transactionReference): void
    {
        // Busca todas as transações com a referência fornecida
        $transactions = Transaction::where('transaction_reference', $transactionReference)->get();

        if ($transactions->isEmpty()) {
            throw new \Exception('Transação não encontrada.');
        }

        // Verifica se já existe uma reversão para qualquer transação com esta referência
        $firstTransaction = $transactions->first();
        $alreadyReversed = Transaction::where('type', 'reversal')
            ->where('description', 'like', "%Reversão da transação #{$firstTransaction->id} (Ref: {$transactionReference})%")
            ->exists();

        if ($alreadyReversed) {
            throw new \Exception('Esta transação já foi revertida anteriormente.');
        }

        $reversalReference = Str::uuid();

        foreach ($transactions as $transaction) {
            $wallet = $transaction->wallet;
            $reversalAmount = -$transaction->amount;

            $wallet->balance += $reversalAmount;
            $wallet->save();

            $wallet->transactions()->create([
                'type' => 'reversal',
                'amount' => $reversalAmount,
                'description' => "Reversão da transação #{$transaction->id} (Ref: {$transactionReference})",
                'transaction_reference' => $reversalReference,
            ]);
        }
    }
}
