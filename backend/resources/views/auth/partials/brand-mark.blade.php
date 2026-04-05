{{-- Large ClinicOS wordmark: flat on page bg � no card, shadow, or border (use transparent PNG for best edge blend). --}}
@props(['subtitle' => ''])
<div class="w-full text-center mb-8 sm:mb-10 md:mb-12">
    <a href="{{ url('/') }}"
       class="inline-flex max-w-full items-center justify-center bg-transparent border-0 shadow-none ring-0 p-0 outline-none focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/35 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm">
        <img
            src="{{ asset('images/clinicos-logo.png') }}"
            alt="ClinicOS"
            width="480"
            height="144"
            loading="eager"
            decoding="async"
            class="mx-auto block w-auto max-w-[min(100%,32rem)] h-24 sm:h-28 md:h-32 lg:h-36 object-contain object-center"
        />
    </a>
    @if($subtitle !== '')
    <p class="mt-4 sm:mt-5 text-sm sm:text-base text-gray-500 max-w-lg mx-auto leading-relaxed">{{ $subtitle }}</p>
    @endif
</div>
@push('scripts')
<script>
(function () {
    console.log('[ClinicOS][auth:brand-mark]', { path: '{{ request()->path() }}', hasSubtitle: {{ $subtitle !== '' ? 'true' : 'false' }} });
})();
</script>
@endpush
