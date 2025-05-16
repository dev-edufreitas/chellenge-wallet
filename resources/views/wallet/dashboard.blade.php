<x-app-layout>
    <div x-data="{ open: false, transactionRef: '' }" class="max-w-5xl mx-auto py-10 px-4 sm:px-6">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">
                Olá, <span class="text-indigo-600">{{ auth()->user()->name }}</span>
            </h1>

            <div class="flex space-x-3 mt-4 sm:mt-0">
                <a href="{{ route('wallet.deposit') }}"
                    class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition duration-150 ease-in-out shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Depositar
                </a>
                <a href="{{ route('wallet.transfer') }}"
                    class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg font-medium transition duration-150 ease-in-out shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m-4 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Transferir
                </a>
            </div>
        </div>

        @if (session('success'))
            <div
                class="mb-6 px-6 py-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-green-500" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error') || $errors->any())
            <div
                class="mb-6 px-6 py-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-red-500" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <span class="font-medium">
                    {{ session('error') ?? $errors->first() }}
                </span>
            </div>
        @endif

        <!-- Grid com cards principais -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Card de saldo -->
            <div
                class="lg:col-span-1 bg-white border border-gray-200 rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-5">
                    <h2 class="text-white text-lg font-medium">Saldo disponível</h2>
                </div>
                <div class="p-6">
                    <p class="text-3xl font-bold text-gray-800 mb-3">
                        R$ {{ number_format($balance, 2, ',', '.') }}
                    </p>
                    <p class="text-sm text-gray-500">Atualizado em {{ now()->format('d/m/Y H:i') }}</p>
                    <p class="text-sm text-gray-500 mt-2">
                        Limite disponível: R$ {{ number_format($wallet->limit, 2, ',', '.') }}
                    </p>
                    @if ($balance < 0)
                        <p class="text-sm text-red-500 mt-1">
                            Limite utilizado: R$ {{ number_format(abs($balance), 2, ',', '.') }}
                        </p>
                    @endif
                </div>

            </div>

            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div
                    class="bg-white border border-gray-200 rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center mb-3">
                        <div class="p-2 rounded-lg bg-blue-100 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-gray-500 text-sm font-medium">Último depósito</h3>
                            @php
                                $lastDeposit = $transactions
                                    ->where('type', 'deposit')
                                    ->sortByDesc('created_at')
                                    ->first();
                            @endphp

                            @if ($lastDeposit)
                                <p class="text-xl font-semibold text-gray-800">
                                    R$ {{ number_format($lastDeposit->amount, 2, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $lastDeposit->created_at->format('d/m/Y H:i') }}
                                </p>
                            @else
                                <p class="text-xl font-semibold text-gray-800">-</p>
                                <p class="text-xs text-gray-500">Nenhum depósito realizado</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white border border-gray-200 rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center mb-3">
                        <div class="p-2 rounded-lg bg-emerald-100 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-gray-500 text-sm font-medium">Transações realizadas</h3>
                            <p class="text-xl font-semibold text-gray-800">{{ $transactions->count() }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Desde {{ now()->subMonth()->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Lista de transações -->
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-md">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Histórico de Transações</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 font-semibold tracking-wider">Tipo</th>
                            <th class="px-6 py-3 font-semibold tracking-wider">Valor</th>
                            <th class="px-6 py-3 font-semibold tracking-wider">Descrição</th>
                            <th class="px-6 py-3 font-semibold tracking-wider">Data</th>
                            <th class="px-6 py-3 font-semibold tracking-wider">Status</th>
                            <th class="px-6 py-3 font-semibold tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if ($transaction->type == 'deposit')
                                            <div class="p-1.5 rounded-full bg-green-100 mr-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="font-medium text-green-600">Depósito</span>
                                        @elseif($transaction->type == 'transfer_in')
                                            <div class="p-1.5 rounded-full bg-blue-100 mr-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 8.586V5z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="font-medium text-blue-600">Recebido</span>
                                        @elseif($transaction->type == 'transfer_out')
                                            <div class="p-1.5 rounded-full bg-purple-100 mr-3">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4 text-purple-600 transform rotate-180"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="font-medium text-purple-600">Enviado</span>
                                        @elseif($transaction->type == 'reversal')
                                            <div class="p-1.5 rounded-full bg-amber-100 mr-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="font-medium text-amber-600">Estorno</span>
                                        @elseif($transaction->type == 'withdrawal')
                                            <div class="p-1.5 rounded-full bg-red-100 mr-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="font-medium text-red-600">Saque</span>
                                        @else
                                            <div class="p-1.5 rounded-full bg-gray-100 mr-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="font-medium text-gray-600">{{ $transaction->type }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">
                                    @if (in_array($transaction->type, ['withdrawal', 'transfer_out']))
                                        <span class="text-red-600">- R$
                                            {{ number_format(abs($transaction->amount), 2, ',', '.') }}</span>
                                    @elseif($transaction->type == 'reversal')
                                        @if ($transaction->amount > 0)
                                            <span class="text-green-600">+ R$
                                                {{ number_format($transaction->amount, 2, ',', '.') }}</span>
                                        @else
                                            <span class="text-red-600">- R$
                                                {{ number_format(abs($transaction->amount), 2, ',', '.') }}</span>
                                        @endif
                                    @else
                                        <span class="text-green-600">+ R$
                                            {{ number_format($transaction->amount, 2, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        {{ $transaction->description ?? '-' }}

                                        <!-- Informações adicionais de estorno -->
                                        @if ($transaction->is_reverted)
                                            <div class="mt-1 flex items-center text-xs text-amber-600">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                <span>Revertido em
                                                    {{ $transaction->reversed_at ? $transaction->reversed_at->format('d/m/Y H:i') : '-' }}</span>
                                            </div>
                                            @if ($transaction->reversal_reason)
                                                <div class="mt-1 text-xs text-gray-500">
                                                    <span class="font-medium">Motivo:</span>
                                                    {{ $transaction->reversal_reason }}
                                                </div>
                                            @endif
                                        @endif

                                        <!-- Para transações de tipo 'reversal', mostrar referência da transação original -->
                                        @if ($transaction->type == 'reversal' && $transaction->original_transaction_reference)
                                            <div class="mt-1 flex items-center text-xs text-gray-500">
                                                <span class="font-medium">Ref. Original:</span>
                                                <span
                                                    class="truncate max-w-xs ml-1">{{ substr($transaction->original_transaction_reference, 0, 8) }}...</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-medium">{{ $transaction->created_at->format('d/m/Y') }}</span>
                                        <span
                                            class="text-xs text-gray-500">{{ $transaction->created_at->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($transaction->is_reverted)
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800">
                                            Revertida
                                        </span>
                                    @elseif($transaction->type == 'reversal')
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800">
                                            Estorno
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Concluída
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if (in_array($transaction->type, ['transfer_in', 'transfer_out']) && !$transaction->is_reverted)
                                        <button type="button"
                                            @click="open = true; transactionRef = '{{ $transaction->transaction_reference }}'"
                                            class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium transition duration-150 ease-in-out shadow-sm">
                                            Reverter
                                        </button>
                                    @elseif($transaction->is_reverted)
                                        <span
                                            class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-md text-sm font-medium">
                                            Revertida
                                        </span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-medium">Nenhuma transação encontrada.</p>
                                    <p class="mt-1 text-sm">Suas transações aparecerão aqui quando você realizar
                                        operações.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-3 border-t flex justify-between items-center text-sm text-gray-600 bg-gray-50">
                <div>
                    Mostrando {{ $transactions->firstItem() ?? 0 }} a {{ $transactions->lastItem() ?? 0 }} de
                    {{ $transactions->total() ?? 0 }} resultados
                </div>
                <div>
                    {{ $transactions->links('vendor.pagination.simple-tailwind') }}
                </div>
            </div>

        </div>

        <!-- Modal de confirmação de estorno -->
        <div class="relative z-10" aria-labelledby="reversal-modal" role="dialog" aria-modal="true" x-show="open"
            x-cloak style="display: none;">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" x-show="open"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                        x-show="open" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-amber-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Confirmar
                                    estorno</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Tem certeza que deseja estornar esta transação?
                                        Esta ação não pode ser desfeita e o saldo será ajustado automaticamente.</p>

                                    <div class="mt-4">
                                        <form id="reversalForm" method="POST"
                                            action="{{ route('wallet.revert') }}">
                                            @csrf
                                            <input type="hidden" name="transaction_reference"
                                                x-model="transactionRef">

                                            <div class="mb-4">
                                                <label for="reversal_reason"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Motivo do
                                                    estorno (opcional)</label>
                                                <textarea name="reversal_reason" id="reversal_reason" rows="2"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    placeholder="Descreva o motivo do estorno"></textarea>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button type="button"
                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                                @click="document.getElementById('reversalForm').submit()">Confirmar estorno</button>
                            <button type="button"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                                @click="open = false">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
