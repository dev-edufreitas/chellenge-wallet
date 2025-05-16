<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 border border-gray-100">
            <!-- Cabeçalho -->
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Transferir Valores</h1>
                </div>
                <p class="text-gray-500 text-sm">Preencha os dados para transferir para outro usuário</p>
            </div>

            <!-- Formulário -->
            <form method="POST" action="{{ route('wallet.transfer.submit') }}">
                @csrf

                <!-- Email do Destinatário -->
                <div class="mb-7">
                    <label for="to_user_email" class="block text-sm font-medium text-gray-700 mb-2">Destinatário</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input 
                            type="email" 
                            name="to_user_email" 
                            id="to_user_email"
                            required
                            placeholder="email@exemplo.com"
                            class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-150 @error('to_user_email') border-red-500 @enderror"
                            value="{{ old('to_user_email') }}"
                        >
                    </div>
                    @error('to_user_email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Valor -->
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
                            class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-150 @error('amount') border-red-500 @enderror"
                            value="{{ old('amount') }}"
                        >
                    </div>
                    @error('amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botão de ação -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8">
                    <a href="{{ route('dashboard') }}" class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium text-center rounded-xl transition-colors">
                        Cancelar
                    </a>
                    <button 
                        type="submit" 
                        class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-[1.02] shadow-md hover:shadow-lg"
                    >
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m-4 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Confirmar Transferência
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>