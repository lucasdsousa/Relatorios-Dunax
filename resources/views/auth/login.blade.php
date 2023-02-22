<x-guest-layout>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-white" />
            <x-text-input id="email" class="block mt-1 w-full text-white" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-white" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" class="text-white" />

            <x-text-input id="password" class="block mt-1 w-full text-white"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-white" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center text-white">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-white shadow-sm focus:ring-indigo-500" name="remember">
            <span class="ml-2 text-sm">{{ __('Lembrar') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm hover:text-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 text-white" href="{{ route('password.request') }}">
                    {{ __('Esqueceu a senha?') }}
                </a>
            @endif

            <x-primary-button class="ml-3">
                {{ __('Entrar') }}
            </x-primary-button>

            <a href="/Cadastrar">
                <x-secondary-button class="ml-3">
                    {{ __('Cadastrar') }}
                </x-secondary-button>
            </a>
        </div>
    </form>
</x-guest-layout>
