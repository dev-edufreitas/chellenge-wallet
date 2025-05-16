<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

// Página inicial
Route::get('/', function () {
    return Auth::check()
        ? Redirect::to('/dashboard')
        : Redirect::to('/login');
});

// Dashboard (com saldo e transações)
Route::get('/dashboard', [WalletController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Grupo de rotas protegidas (usuário autenticado)
Route::middleware(['auth'])->group(function () {
    // Perfil do usuário (padrão do Breeze)
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Páginas visuais da carteira
    Route::get('/deposit',    [WalletController::class, 'showDepositForm'])->name('wallet.deposit');
    Route::get('/transfer',   [WalletController::class, 'showTransferForm'])->name('wallet.transfer');

    // Ações financeiras
    Route::post('/deposit',   [WalletController::class, 'deposit'])->name('wallet.deposit.submit');
    Route::post('/transfer',  [WalletController::class, 'transfer'])->name('wallet.transfer.submit');
    Route::post('/revert',    [WalletController::class, 'revertTransaction'])->name('wallet.revert');
    Route::get('/balance',    [WalletController::class, 'getBalance'])->name('wallet.balance');
});

// Inclui rotas de login, registro etc. (padrão do Breeze)
require __DIR__.'/auth.php';