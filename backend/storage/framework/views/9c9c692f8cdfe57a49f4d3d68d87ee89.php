
<?php
    $active = $active ?? 'index';
    \Illuminate\Support\Facades\Log::debug('analytics.subnav.render', ['active' => $active]);
?>
<nav class="flex flex-wrap gap-1 sm:gap-2 border-b border-gray-200 mb-6 -mx-1 px-1" aria-label="Analytics sections">
    <a href="<?php echo e(route('analytics.index')); ?>"
       class="inline-flex items-center gap-2 px-3 sm:px-4 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 -mb-px transition-colors whitespace-nowrap
       <?php echo e($active === 'index' ? 'text-brand-blue border-brand-blue bg-white' : 'text-gray-600 border-transparent hover:text-gray-900 hover:border-gray-200'); ?>">
        <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        Dashboard
    </a>
    <a href="<?php echo e(route('analytics.prescriptions')); ?>"
       class="inline-flex items-center gap-2 px-3 sm:px-4 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 -mb-px transition-colors whitespace-nowrap
       <?php echo e($active === 'prescriptions' ? 'text-brand-blue border-brand-blue bg-white' : 'text-gray-600 border-transparent hover:text-gray-900 hover:border-gray-200'); ?>">
        <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
        Prescription Analytics
    </a>
    <a href="<?php echo e(route('analytics.revenue')); ?>"
       class="inline-flex items-center gap-2 px-3 sm:px-4 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 -mb-px transition-colors whitespace-nowrap
       <?php echo e($active === 'revenue' ? 'text-brand-blue border-brand-blue bg-white' : 'text-gray-600 border-transparent hover:text-gray-900 hover:border-gray-200'); ?>">
        <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 16l4-4 4 4 6-6"/></svg>
        Revenue Report
    </a>
    <a href="<?php echo e(route('analytics.patients')); ?>"
       class="inline-flex items-center gap-2 px-3 sm:px-4 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 -mb-px transition-colors whitespace-nowrap
       <?php echo e($active === 'patients' ? 'text-brand-blue border-brand-blue bg-white' : 'text-gray-600 border-transparent hover:text-gray-900 hover:border-gray-200'); ?>">
        <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        Patient Report
    </a>
</nav>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/analytics/partials/subnav.blade.php ENDPATH**/ ?>