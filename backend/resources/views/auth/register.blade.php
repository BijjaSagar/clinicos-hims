@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<form method="POST" action="{{ route('register.post') }}" class="w-full max-w-5xl mx-auto" x-data="{ plan: '{{ old('plan', 'professional') }}' }">
    @csrf

    @include('auth.partials.brand-mark', ['subtitle' => 'Create your clinic · 14-day free trial'])

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        {{-- Plans: compact column --}}
        <div class="lg:col-span-4 space-y-3">
            <p class="text-sm font-semibold text-gray-800">Choose plan</p>
            <p class="text-xs text-gray-500 -mt-1">All plans include a free trial — cancel anytime.</p>
            <div class="space-y-2">
                @php
                $plans = [
                    ['key'=>'starter',     'name'=>'Starter',     'price'=>'₹2,999', 'features'=>'EMR, Billing, WhatsApp'],
                    ['key'=>'professional','name'=>'Pro',         'price'=>'₹5,999', 'features'=>'+ Analytics, ABDM'],
                    ['key'=>'hospital',    'name'=>'Hospital',    'price'=>'₹14,999','features'=>'+ IPD, Pharmacy, Lab'],
                ];
                @endphp
                @foreach($plans as $p)
                <label class="block cursor-pointer">
                    <input type="radio" name="plan" value="{{ $p['key'] }}" class="sr-only" x-model="plan"
                        {{ old('plan', 'professional') === $p['key'] ? 'checked' : '' }}>
                    <div
                        class="rounded-xl border-2 px-3 py-2.5 transition-all"
                        :class="plan === '{{ $p['key'] }}'
                            ? 'border-blue-500 bg-blue-50/80 ring-2 ring-blue-500/20'
                            : 'border-gray-200 bg-white hover:border-gray-300'"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-bold text-gray-900">{{ $p['name'] }}</span>
                            <span class="text-sm font-extrabold text-blue-600">{{ $p['price'] }}<span class="text-xs font-normal text-gray-500">/mo</span></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $p['features'] }}</p>
                    </div>
                </label>
                @endforeach
            </div>
            <p class="text-xs text-gray-400">No credit card required to start.</p>
        </div>

        {{-- Form: dense grid to reduce scroll --}}
        <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-100 shadow-lg shadow-gray-200/40 p-5 sm:p-6 lg:p-7">
            <h2 class="text-lg font-bold text-gray-900 font-display">Account details</h2>
            <p class="text-sm text-gray-500 mt-0.5 mb-5">We&apos;ll set up your clinic workspace after you register.</p>

            @if(session('error'))
            <div class="bg-red-50 border border-red-100 rounded-xl p-3 mb-4 text-red-800 text-sm" role="alert">{{ session('error') }}</div>
            @endif
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3 mb-4 text-emerald-800 text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
            <div class="bg-red-50 border border-red-100 rounded-xl p-3 mb-4">
                <ul class="text-red-700 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-1">
                    <label for="name" class="block text-sm font-semibold text-gray-800 mb-1">Full name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white"
                        placeholder="Dr. Sharma" required autocomplete="name">
                </div>
                <div class="sm:col-span-1">
                    <label for="phone" class="block text-sm font-semibold text-gray-800 mb-1">Phone</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white"
                        placeholder="+91 98765 43210" required autocomplete="tel">
                </div>
                <div class="sm:col-span-2">
                    <label for="email" class="block text-sm font-semibold text-gray-800 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white"
                        placeholder="doctor@clinic.com" required autocomplete="email">
                </div>
                <div class="sm:col-span-1">
                    <label for="clinic_name" class="block text-sm font-semibold text-gray-800 mb-1">Clinic name</label>
                    <input type="text" name="clinic_name" id="clinic_name" value="{{ old('clinic_name') }}"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white"
                        placeholder="Sharma Skin Clinic" required autocomplete="organization">
                </div>
                <div class="sm:col-span-1">
                    <label for="specialty" class="block text-sm font-semibold text-gray-800 mb-1">Specialty</label>
                    <select name="specialty" id="specialty"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white"
                        required>
                        <option value="">Select specialty</option>
                        <option value="dermatology" @selected(old('specialty') === 'dermatology')>Dermatology</option>
                        <option value="physiotherapy" @selected(old('specialty') === 'physiotherapy')>Physiotherapy</option>
                        <option value="dental" @selected(old('specialty') === 'dental')>Dental</option>
                        <option value="ophthalmology" @selected(old('specialty') === 'ophthalmology')>Ophthalmology</option>
                        <option value="orthopedics" @selected(old('specialty') === 'orthopedics')>Orthopedics</option>
                        <option value="ent" @selected(old('specialty') === 'ent')>ENT</option>
                        <option value="gynecology" @selected(old('specialty') === 'gynecology')>Gynecology</option>
                        <option value="general" @selected(old('specialty') === 'general')>General Practice</option>
                    </select>
                </div>
                <div class="sm:col-span-1">
                    <label for="password" class="block text-sm font-semibold text-gray-800 mb-1">Password</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white"
                        placeholder="Min. 8 characters" required autocomplete="new-password">
                </div>
                <div class="sm:col-span-1">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-800 mb-1">Confirm</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white"
                        placeholder="Repeat password" required autocomplete="new-password">
                </div>
            </div>

            <button type="submit"
                class="mt-6 w-full py-3 px-4 rounded-xl text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2"
                style="background: linear-gradient(135deg, #1447E6 0%, #0891B2 100%);">
                Create account
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>

            <p class="mt-5 text-center text-sm text-gray-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Sign in</a>
            </p>
        </div>
    </div>
</form>
@push('scripts')
<script>
(function () {
    console.log('[ClinicOS][auth:register]', { route: 'register', path: window.location.pathname });
})();
</script>
@endpush
@endsection
