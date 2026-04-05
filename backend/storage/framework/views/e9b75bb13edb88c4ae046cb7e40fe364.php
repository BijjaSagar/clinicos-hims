<?php $__env->startSection('title', 'Create Clinic'); ?>
<?php $__env->startSection('subtitle', 'Onboard a new clinic to the platform'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl">
    <form action="<?php echo e(route('admin.clinics.store')); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>

        
        <div class="bg-white rounded-xl p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Clinic Information</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Clinic Name *</label>
                    <input type="text" name="clinic_name" value="<?php echo e(old('clinic_name')); ?>" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Sharma Skin Clinic">
                    <?php $__errorArgs = ['clinic_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Specialty</label>
                    <select name="specialty" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Specialty</option>
                        <option value="general">General Practice</option>
                        <option value="dermatology">Dermatology</option>
                        <option value="dental">Dental</option>
                        <option value="ophthalmology">Ophthalmology</option>
                        <option value="pediatrics">Pediatrics</option>
                        <option value="orthopedics">Orthopedics</option>
                        <option value="cardiology">Cardiology</option>
                        <option value="gynecology">Gynecology</option>
                        <option value="physiotherapy">Physiotherapy</option>
                        <option value="ent">ENT</option>
                        <option value="psychiatry">Psychiatry</option>
                        <option value="ayurveda">Ayurveda</option>
                        <option value="homeopathy">Homeopathy</option>
                        <option value="multi_specialty">Multi-Specialty</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Plan *</label>
                    <select name="plan" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="trial" <?php echo e(old('plan') === 'trial' ? 'selected' : ''); ?>>Trial (30 days free)</option>
                        <option value="solo" <?php echo e(old('plan') === 'solo' ? 'selected' : ''); ?>>Solo (₹999/month)</option>
                        <option value="small" <?php echo e(old('plan') === 'small' ? 'selected' : ''); ?>>Small (₹2,499/month)</option>
                        <option value="group" <?php echo e(old('plan') === 'group' ? 'selected' : ''); ?>>Group (₹4,999/month)</option>
                        <option value="enterprise" <?php echo e(old('plan') === 'enterprise' ? 'selected' : ''); ?>>Enterprise (Custom)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">City</label>
                    <input type="text" name="city" value="<?php echo e(old('city')); ?>"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Mumbai">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">State</label>
                    <input type="text" name="state" value="<?php echo e(old('state')); ?>"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Maharashtra">
                </div>

                <div id="trial-days-field" class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Trial Period (Days)</label>
                    <input type="number" name="trial_days" value="<?php echo e(old('trial_days', 30)); ?>" min="1" max="365"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-xl p-6 border border-gray-100" x-data="{
            facilityType: '<?php echo e(old('facility_type', 'clinic')); ?>',
            selectAll: false,
            himsFeatures: {
                <?php $__currentLoopData = array_keys(config('hims_expansion.hims_feature_keys')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                '<?php echo e($key); ?>': <?php echo e(old('hims_features.' . $key) ? 'true' : 'false'); ?>,
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            },
            toggleAll() {
                this.selectAll = !this.selectAll;
                Object.keys(this.himsFeatures).forEach(k => this.himsFeatures[k] = this.selectAll);
            }
        }">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Hospital / HIMS Configuration</h3>

            <div class="grid grid-cols-2 gap-4 mb-6">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Facility Type</label>
                    <select name="facility_type" x-model="facilityType"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <?php $__currentLoopData = config('hims_expansion.facility_types'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e(old('facility_type', 'clinic') === $value ? 'selected' : ''); ?>><?php echo e($meta['label']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['facility_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div x-show="facilityType !== 'clinic'" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Licensed Beds</label>
                    <input type="number" name="licensed_beds" min="0" value="<?php echo e(old('licensed_beds')); ?>"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="0">
                    <?php $__errorArgs = ['licensed_beds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Subdomain</label>
                <p class="text-sm text-gray-500">&lt;slug&gt;.clinic0s.com — auto-generated from clinic name</p>
            </div>

            
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-medium text-gray-700">HIMS Features</label>
                    <button type="button" @click="toggleAll()"
                        class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                        Select All Hospital Features
                    </button>
                </div>

                <div class="space-y-4">
                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Bed & Ward</p>
                        <div class="flex flex-wrap gap-3">
                            <?php $__currentLoopData = ['bed_management']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="hims_features[<?php echo e($key); ?>]" value="1"
                                    x-model="himsFeatures['<?php echo e($key); ?>']"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <?php echo e(config('hims_expansion.hims_feature_keys.' . $key)); ?>

                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">OPD</p>
                        <div class="flex flex-wrap gap-3">
                            <?php $__currentLoopData = ['opd_hospital']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="hims_features[<?php echo e($key); ?>]" value="1"
                                    x-model="himsFeatures['<?php echo e($key); ?>']"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <?php echo e(config('hims_expansion.hims_feature_keys.' . $key)); ?>

                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">IPD</p>
                        <div class="flex flex-wrap gap-3">
                            <?php $__currentLoopData = ['ipd', 'emergency']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="hims_features[<?php echo e($key); ?>]" value="1"
                                    x-model="himsFeatures['<?php echo e($key); ?>']"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <?php echo e(config('hims_expansion.hims_feature_keys.' . $key)); ?>

                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pharmacy</p>
                        <div class="flex flex-wrap gap-3">
                            <?php $__currentLoopData = ['pharmacy_inventory', 'pharmacy_ip_dispensing', 'pharmacy_op_dispensing', 'pharmacy_purchase_grn', 'pharmacy_returns']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="hims_features[<?php echo e($key); ?>]" value="1"
                                    x-model="himsFeatures['<?php echo e($key); ?>']"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <?php echo e(config('hims_expansion.hims_feature_keys.' . $key)); ?>

                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Lab / LIS</p>
                        <div class="flex flex-wrap gap-3">
                            <?php $__currentLoopData = ['lis_collection', 'lis_processing', 'lis_results', 'lis_reports_pdf', 'lis_hl7']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="hims_features[<?php echo e($key); ?>]" value="1"
                                    x-model="himsFeatures['<?php echo e($key); ?>']"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <?php echo e(config('hims_expansion.hims_feature_keys.' . $key)); ?>

                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Billing</p>
                        <div class="flex flex-wrap gap-3">
                            <?php $__currentLoopData = ['billing_unified', 'billing_insurance_extended', 'billing_credit_corporate', 'billing_gst_slabs', 'mis_revenue']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="hims_features[<?php echo e($key); ?>]" value="1"
                                    x-model="himsFeatures['<?php echo e($key); ?>']"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <?php echo e(config('hims_expansion.hims_feature_keys.' . $key)); ?>

                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nursing</p>
                        <div class="flex flex-wrap gap-3">
                            <?php $__currentLoopData = ['nursing_notes', 'mar', 'vitals_chart', 'nursing_care_plans', 'nursing_handover']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="hims_features[<?php echo e($key); ?>]" value="1"
                                    x-model="himsFeatures['<?php echo e($key); ?>']"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <?php echo e(config('hims_expansion.hims_feature_keys.' . $key)); ?>

                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Analytics</p>
                        <div class="flex flex-wrap gap-3">
                            <?php $__currentLoopData = ['analytics_census', 'analytics_lab_tat', 'analytics_pharmacy_alerts', 'analytics_opd']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="hims_features[<?php echo e($key); ?>]" value="1"
                                    x-model="himsFeatures['<?php echo e($key); ?>']"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <?php echo e(config('hims_expansion.hims_feature_keys.' . $key)); ?>

                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php echo $__env->make('admin.clinics.partials.product-modules', ['enabledProductModuleKeys' => $enabledProductModuleKeys], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <div class="bg-white rounded-xl p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Owner Account</h3>
            <p class="text-sm text-gray-500 mb-4">This person will have full admin access to the clinic.</p>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name *</label>
                    <input type="text" name="owner_name" value="<?php echo e(old('owner_name')); ?>" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Dr. Priya Sharma">
                    <?php $__errorArgs = ['owner_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email *</label>
                    <input type="email" name="owner_email" value="<?php echo e(old('owner_email')); ?>" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="doctor@clinic.com">
                    <?php $__errorArgs = ['owner_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone *</label>
                    <input type="text" name="owner_phone" value="<?php echo e(old('owner_phone')); ?>" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="+91 98765 43210">
                    <?php $__errorArgs = ['owner_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password *</label>
                    <input type="password" name="owner_password" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="••••••••">
                    <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                    <?php $__errorArgs = ['owner_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>

        
        <div class="flex items-center justify-between">
            <a href="<?php echo e(route('admin.clinics.index')); ?>" class="px-6 py-2.5 text-gray-700 hover:text-gray-900">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors">
                Create Clinic
            </button>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    const planSelect = document.querySelector('[name="plan"]');
    const trialDaysField = document.getElementById('trial-days-field');
    
    function toggleTrialDays() {
        trialDaysField.style.display = planSelect.value === 'trial' ? 'block' : 'none';
    }
    
    planSelect.addEventListener('change', toggleTrialDays);
    toggleTrialDays();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/admin/clinics/create.blade.php ENDPATH**/ ?>