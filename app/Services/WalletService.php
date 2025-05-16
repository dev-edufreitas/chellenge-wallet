<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class WalletService
{
    /**
     * Realiza um depósito na carteira do usuário
     * 
     * @param User $user Usuário que receberá o depósito
     * @param float $amount Valor do depósito
     * @param string|null $description Descrição opcional
     */
    public function deposit(User $user, float $amount, $description = null): void
    {
        $wallet = $user->wallet;

        $wallet->balance += $amount;
        $wallet->save();

        $wallet->transactions()->create([
            'type' => 'deposit',
            'amount' => $amount,
            'description' => $description,
            'transaction_reference' => Str::uuid(),
        ]);
    }

    /**
     * Transfere um valor entre usuários
     * 
     * @param User $fromUser Usuário de origem
     * @param User $toUser Usuário de destino
     * @param float $amount Valor a ser transferido
     * @throws \Exception Se não houver saldo suficiente
     */
    public function transfer(User $fromUser, User $toUser, float $amount): void
    {
        // Verifica se o saldo + limite é suficiente para a transferência
        if ($fromUser->wallet->balance + $fromUser->wallet->limit < $amount) {
            throw new \Exception('Saldo e limite insuficientes para realizar a transferência.');
        }
        
        if ($fromUser->email === $toUser->email) {
            throw new \Exception('Você não pode realizar uma transferencia para você mesmo.');
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

    /**
     * Reverte uma transação usando sua referência
     * 
     * @param string $transactionReference Referência UUID da transação original
     * @param string|null $reason Motivo opcional do estorno
     * @throws \Exception Se a transação não existir ou já tiver sido revertida
     */
    public function revertTransaction(string $transactionReference, $reason = null): void
    {
        // Busca todas as transações com a referência fornecida
        $transactions = Transaction::where('transaction_reference', $transactionReference)->get();

        if ($transactions->isEmpty()) {
            throw new \Exception('Transação não encontrada.');
        }

        // Verifica se qualquer transação já foi revertida
        if ($transactions->contains('is_reverted', true)) {
            throw new \Exception('Esta transação já foi revertida anteriormente.');
        }

        $reversalReference = Str::uuid();
        $reversalReason    = $reason ?? 'Solicitação do usuário';

        foreach ($transactions as $transaction) {
            $wallet         = $transaction->wallet;
            $reversalAmount = -$transaction->amount;

            // Atualizar o saldo da carteira
            $wallet->balance += $reversalAmount;
            $wallet->save();

            // Marcar a transação original como revertida
            $transaction->is_reverted     = true;
            $transaction->reversal_reason = $reversalReason;
            $transaction->reversed_at     = now();
            $transaction->save();

            // Criar a transação de estorno
            $wallet->transactions()->create([
                'type'                           => 'reversal',
                'amount'                         => $reversalAmount,
                'description'                    => "Reversão da transação #{$transaction->id}",
                'transaction_reference'          => $reversalReference,
                'original_transaction_reference' => $transactionReference,
                'reversal_reason'                => $reversalReason,
                'is_reverted'                    => false,
            ]);
        }

        Log::info("Transação {$transactionReference} revertida com sucesso. Motivo: {$reversalReason}");
    }
}
