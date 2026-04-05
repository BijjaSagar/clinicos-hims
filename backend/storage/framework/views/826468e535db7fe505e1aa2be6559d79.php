<?php $__env->startSection('title', 'Settings'); ?>
<?php $__env->startSection('breadcrumb', 'Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 space-y-6">
    <?php if(session('success')): ?>
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-900"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <nav class="space-y-1">
                    <a href="#clinic" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Clinic Info
                    </a>
                    <a href="#billing" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                        </svg>
                        Billing & GST
                    </a>
                    <a href="#users" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Users & Staff
                    </a>
                    <a href="#notifications" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Notifications
                    </a>
                    <a href="#ai-apis" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        AI &amp; APIs
                    </a>
                </nav>
            </div>
        </div>

        
        <div class="lg:col-span-2 space-y-6">
            
            <div id="clinic" class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900">Clinic Information</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Update your clinic details and contact info</p>
                </div>
                <form action="<?php echo e(route('settings.clinic')); ?>" method="POST" class="p-5 space-y-4">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Clinic Name</label>
                            <input type="text" name="name" value="<?php echo e($clinic->name ?? ''); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Specialty</label>
                            <?php
                                $specialties = $clinic->specialties ?? [];
                                $currentSpecialty = is_array($specialties) ? ($specialties[0] ?? '') : $specialties;
                            ?>
                            <select name="specialty" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="dermatology" <?php echo e($currentSpecialty == 'dermatology' ? 'selected' : ''); ?>>Dermatology</option>
                                <option value="physiotherapy" <?php echo e($currentSpecialty == 'physiotherapy' ? 'selected' : ''); ?>>Physiotherapy</option>
                                <option value="dental" <?php echo e($currentSpecialty == 'dental' ? 'selected' : ''); ?>>Dental</option>
                                <option value="ophthalmology" <?php echo e($currentSpecialty == 'ophthalmology' ? 'selected' : ''); ?>>Ophthalmology</option>
                                <option value="general" <?php echo e($currentSpecialty == 'general' ? 'selected' : ''); ?>>General Practice</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                        <textarea name="address_line1" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo e($clinic->address_line1 ?? ''); ?></textarea>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">City</label>
                            <input type="text" name="city" value="<?php echo e($clinic->city ?? ''); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">State</label>
                            <input type="text" name="state" value="<?php echo e($clinic->state ?? ''); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Pincode</label>
                            <input type="text" name="pincode" value="<?php echo e($clinic->pincode ?? ''); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                            <input type="tel" name="phone" value="<?php echo e($clinic->phone ?? ''); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                            <input type="email" name="email" value="<?php echo e($clinic->email ?? ''); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50/80 p-4 space-y-3">
                        <p class="text-sm font-semibold text-slate-800">Legal &amp; tax registration</p>
                        <p class="text-xs text-slate-600">GSTIN, PAN, and facility registration number are stored on your clinic profile (not in the letterhead text) and print in the highlighted strip on <strong>GST tax invoices</strong>. Only the clinic owner can change Settings.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">GSTIN (15 characters)</label>
                                <input type="text" name="gstin" value="<?php echo e(old('gstin', $clinic->gstin ?? '')); ?>" maxlength="15" autocomplete="off" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase <?php $__errorArgs = ['gstin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="22AAAAA0000A1Z5">
                            <?php $__errorArgs = ['gstin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">PAN (10 characters)</label>
                                <input type="text" name="pan" value="<?php echo e(old('pan', $clinic->pan ?? '')); ?>" maxlength="10" autocomplete="off" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase <?php $__errorArgs = ['pan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="AAAAA0000A">
                            <?php $__errorArgs = ['pan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Facility / clinical establishment registration (optional)</label>
                            <input type="text" name="registration_number" value="<?php echo e(old('registration_number', $clinic->registration_number ?? '')); ?>" maxlength="120" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. State council registration, NABH ID, hospital licence no.">
                            <p class="text-xs text-gray-500 mt-1">Shown on invoices as &quot;Reg.&quot; when set.</p>
                        </div>
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            
            <div id="billing" class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900">Billing Settings</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Configure invoice numbering and GST</p>
                </div>
                <form action="<?php echo e(route('settings.billing')); ?>" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Invoice Prefix</label>
                            <input type="text" name="invoice_prefix" value="<?php echo e($clinic->settings['invoice_prefix'] ?? 'CLN'); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Default GST Rate (%)</label>
                            <input type="number" name="default_gst_rate" value="<?php echo e($clinic->settings['default_gst_rate'] ?? 18); ?>" step="0.5" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Default PDF type</label>
                        <select name="default_invoice_format" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="gst" <?php echo e(($clinic->settings['default_invoice_format'] ?? 'gst') === 'gst' ? 'selected' : ''); ?>>GST tax invoice (CGST/SGST break-up)</option>
                            <option value="bill" <?php echo e(($clinic->settings['default_invoice_format'] ?? '') === 'bill' ? 'selected' : ''); ?>>Simple bill (no GST columns — consolidated amounts)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">You can still download either format from each invoice page.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Invoice logo (prints on PDF)</label>
                        <?php if(!empty($clinic->settings['invoice_logo_path'])): ?>
                        <div class="flex items-center gap-3 mb-2">
                            <img src="<?php echo e(asset('storage/'.$clinic->settings['invoice_logo_path'])); ?>" alt="Logo" class="h-12 object-contain border border-gray-200 rounded-lg bg-white p-1">
                            <label class="inline-flex items-center gap-2 text-sm text-red-600">
                                <input type="checkbox" name="remove_invoice_logo" value="1" class="rounded border-gray-300"> Remove logo
                            </label>
                        </div>
                        <?php endif; ?>
                        <input type="file" name="invoice_logo" accept="image/jpeg,image/png,image/gif,image/webp" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700">
                        <p class="text-xs text-gray-500 mt-1">PNG/JPG up to 3MB. Shown on the top-left of every invoice PDF.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Letterhead / printed address (optional)</label>
                        <textarea name="invoice_letterhead" rows="5" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm" placeholder="If empty, clinic address from Clinic Information is used."><?php echo e($clinic->settings['invoice_letterhead'] ?? ''); ?></textarea>
                        <p class="text-xs text-gray-500 mt-1">Use line breaks. Include legal name, full address, phone, email as you want them on the bill.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tagline under name (optional)</label>
                        <input type="text" name="invoice_tagline" value="<?php echo e($clinic->settings['invoice_tagline'] ?? ''); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. NABH Accredited · Multi-specialty Hospital">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Payment Terms</label>
                        <textarea name="payment_terms" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Payment due within 7 days"><?php echo e($clinic->settings['payment_terms'] ?? ''); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Footer note (optional)</label>
                        <textarea name="invoice_footer" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Bank: … · UPI: … · Subject to Pune jurisdiction"><?php echo e($clinic->settings['invoice_footer'] ?? ''); ?></textarea>
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                            Save Billing Settings
                        </button>
                    </div>
                </form>
            </div>

            
            <div id="ai-apis" class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900">AI &amp; APIs</h3>
                    <p class="text-sm text-gray-500 mt-0.5">OpenAI powers voice transcription (Whisper) and note generation; Anthropic Claude powers EMR field mapping and summaries. Keys are encrypted at rest. Leave password fields blank to keep existing keys.</p>
                </div>
                <form action="<?php echo e(route('settings.ai-credentials')); ?>" method="POST" class="p-5 space-y-4">
                    <?php echo csrf_field(); ?>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700 space-y-1">
                        <p><strong>Status:</strong>
                            OpenAI <?php echo e(!empty($aiOpenaiConfigured) ? '✓ configured' : '— use .env OPENAI_API_KEY or enter below'); ?> ·
                            Anthropic <?php echo e(!empty($aiAnthropicConfigured) ? '✓ configured' : '— use .env ANTHROPIC_API_KEY or enter below'); ?>

                        </p>
                        <p class="text-xs text-slate-600">Deployment fallback: <code class="bg-white px-1 rounded">OPENAI_API_KEY</code> and <code class="bg-white px-1 rounded">ANTHROPIC_API_KEY</code> in <code class="bg-white px-1 rounded">.env</code> apply when no clinic key is set.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">OpenAI API key</label>
                        <input type="password" name="openai_api_key" autocomplete="off" placeholder="<?php echo e(!empty($aiOpenaiConfigured) ? '•••••••• (saved — leave blank to keep)' : 'sk-…'); ?>"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm">
                        <label class="inline-flex items-center gap-2 mt-2 text-sm text-red-700">
                            <input type="checkbox" name="clear_openai_key" value="1" class="rounded border-gray-300"> Remove stored OpenAI key
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Anthropic API key (Claude)</label>
                        <input type="password" name="anthropic_api_key" autocomplete="off" placeholder="<?php echo e(!empty($aiAnthropicConfigured) ? '•••••••• (saved — leave blank to keep)' : 'sk-ant-…'); ?>"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm">
                        <label class="inline-flex items-center gap-2 mt-2 text-sm text-red-700">
                            <input type="checkbox" name="clear_anthropic_key" value="1" class="rounded border-gray-300"> Remove stored Anthropic key
                        </label>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">OpenAI chat model</label>
                            <input type="text" name="ai_openai_chat_model" value="<?php echo e(old('ai_openai_chat_model', $clinic->settings['ai_openai_chat_model'] ?? '')); ?>" placeholder="gpt-4o"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 font-mono text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">OpenAI fast model</label>
                            <input type="text" name="ai_openai_fast_model" value="<?php echo e(old('ai_openai_fast_model', $clinic->settings['ai_openai_fast_model'] ?? '')); ?>" placeholder="gpt-4o-mini"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 font-mono text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Anthropic model</label>
                            <input type="text" name="ai_anthropic_model" value="<?php echo e(old('ai_anthropic_model', $clinic->settings['ai_anthropic_model'] ?? '')); ?>" placeholder="claude-haiku-4-5"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 font-mono text-sm">
                        </div>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
                            Save AI settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/settings/index.blade.php ENDPATH**/ ?>