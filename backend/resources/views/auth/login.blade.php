@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-md mx-auto flex flex-col justify-center flex-1 min-h-0">
    @include('auth.partials.brand-mark', ['subtitle' => 'Sign in to your clinic workspace'])

    <div class="bg-white rounded-2xl border border-gray-100 shadow-lg shadow-gray-200/40 p-6 sm:p-8">
        <h1 class="text-xl font-bold text-gray-900 font-display tracking-tight">Welcome back</h1>
        <p class="text-gray-500 text-sm mt-1 mb-6">Use your ClinicOS account email and password.</p>

        @if($errors->any())
        <div class="bg-red-50 border border-red-100 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-2 text-red-700 text-sm">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-800 mb-1.5">Email address</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/80 text-gray-900 placeholder-gray-400 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white transition-colors"
                    placeholder="doctor@clinic.com"
                    required
                    autofocus
                    autocomplete="email"
                >
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-semibold text-gray-800">Password</label>
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Forgot password?</a>
                </div>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/80 text-gray-900 placeholder-gray-400 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white transition-colors"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
            </div>

            <div class="flex items-center">
                <input
                    type="checkbox"
                    name="remember"
                    id="remember"
                    class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                >
                <label for="remember" class="ml-2 text-sm text-gray-600">Remember me for 30 days</label>
            </div>

            <button
                type="submit"
                class="w-full py-3 px-4 rounded-xl text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2"
                style="background: linear-gradient(135deg, #1447E6 0%, #0891B2 100%);"
            >
                Sign in
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Don&apos;t have an account?
            <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700">Create one</a>
        </p>
    </div>

    <div class="mt-6 rounded-xl border border-dashed border-gray-200/90 bg-gray-50/40 px-4 py-3 text-center text-xs text-gray-500">
        <p class="font-medium text-gray-600 mb-1">Demo</p>
        <p><span class="text-gray-400">Email</span> <code class="text-gray-800 bg-gray-100 px-1.5 py-0.5 rounded">demo@clinicos.com</code></p>
        <p class="mt-0.5"><span class="text-gray-400">Password</span> <code class="text-gray-800 bg-gray-100 px-1.5 py-0.5 rounded">password</code></p>
    </div>
</div>
@push('scripts')
<script>
(function () {
    console.log('[ClinicOS][auth:login]', { route: 'login', path: window.location.pathname });
})();
</script>
@endpush
@endsection
