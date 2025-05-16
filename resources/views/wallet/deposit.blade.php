<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 border border-gray-100">
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Realizar Depósito</h1>
                </div>
                <p class="text-gray-500 text-sm">Preencha os dados abaixo para adicionar fundos à sua carteira</p>
            </div>
            <form method="POST" action="{{ route('wallet.deposit.submit') }}">
                @csrf
                <div class="mb-7">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Valor (R$)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                            <span class="text-gray-400">R$</span>
                        </div>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="amount" 
                            id="amount"
                            required
                            placeholder="0,00"
                            class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 @error('amount') border-red-500 @enderror"
                            value="{{ old('amount') }}"
                        >
                    </div>
                    @error('amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descrição -->
                <div class="mb-10">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrição (opcional)</label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 @error('description') border-red-500 @enderror"
                        placeholder="Ex: Depósito inicial"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8">
                    <a href="{{ route('dashboard') }}" class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium text-center rounded-xl transition-colors">
                        Cancelar
                    </a>
                    <button 
                        type="submit" 
                        class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-[1.02] shadow-md hover:shadow-lg"
                    >
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Confirmar Depósito
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>