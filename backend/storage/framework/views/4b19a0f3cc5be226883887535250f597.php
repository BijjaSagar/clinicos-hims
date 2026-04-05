<?php $__env->startSection('title', 'Edit Clinic'); ?>
<?php $__env->startSection('subtitle', $clinic->name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl">
    <form action="<?php echo e(route('admin.clinics.update', $clinic)); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        
        <div class="bg-white rounded-xl p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Clinic Information</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Clinic Name *</label>
                    <input type="text" name="name" value="<?php echo e(old('name', $clinic->name)); ?>" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <?php $__errorArgs = ['name'];
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
                    <?php
                        $currentSpecialty = is_array($clinic->specialties) ? ($clinic->specialties[0] ?? '') : $clinic->specialties;
                    ?>
                    <select name="specialty" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Specialty</option>
                        <?php $__currentLoopData = ['general', 'dermatology', 'dental', 'ophthalmology', 'pediatrics', 'orthopedics', 'cardiology', 'gynecology', 'physiotherapy', 'ent', 'psychiatry', 'ayurveda', 'homeopathy', 'multi_specialty']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($spec); ?>" <?php echo e($currentSpecialty === $spec ? 'selected' : ''); ?>><?php echo e(ucfirst(str_replace('_', ' ', $spec))); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Plan *</label>
                    <select name="plan" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="trial" <?php echo e(old('plan', $clinic->plan) === 'trial' ? 'selected' : ''); ?>>Trial</option>
                        <option value="solo" <?php echo e(old('plan', $clinic->plan) === 'solo' ? 'selected' : ''); ?>>Solo (₹999/month)</option>
                        <option value="small" <?php echo e(old('plan', $clinic->plan) === 'small' ? 'selected' : ''); ?>>Small (₹2,499/month)</option>
                        <option value="group" <?php echo e(old('plan', $clinic->plan) === 'group' ? 'selected' : ''); ?>>Group (₹4,999/month)</option>
                        <option value="enterprise" <?php echo e(old('plan', $clinic->plan) === 'enterprise' ? 'selected' : ''); ?>>Enterprise (Custom)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $clinic->email)); ?>"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="<?php echo e(old('phone', $clinic->phone)); ?>"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">City</label>
                    <input type="text" name="city" value="<?php echo e(old('city', $clinic->city)); ?>"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">State</label>
                    <input type="text" name="state" value="<?php echo e(old('state', $clinic->state)); ?>"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">GSTIN</label>
                    <input type="text" name="gstin" value="<?php echo e(old('gstin', $clinic->gstin)); ?>"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="27AADCS1234B1ZP">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Trial Ends At</label>
                    <input type="date" name="trial_ends_at" value="<?php echo e(old('trial_ends_at', $clinic->trial_ends_at?->format('Y-m-d'))); ?>"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $clinic->is_active) ? 'checked' : ''); ?>

                            class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Clinic is active</span>
                    </label>
                </div>
            </div>
        </div>

        
        <?php
            $currentHimsFeatures = is_array($clinic->hims_features) ? $clinic->hims_features : (is_string($clinic->hims_features) ? json_decode($clinic->hims_features, true) : []);
            $currentHimsFeatures = $currentHimsFeatures ?: [];
        ?>
        <div class="bg-white rounded-xl p-6 border border-gray-100" x-data="{
            facilityType: '<?php echo e(old('facility_type', $clinic->facility_type ?? 'clinic')); ?>',
            selectAll: false,
            himsFeatures: {
                <?php $__currentLoopData = array_keys(config('hims_expansion.hims_feature_keys')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                '<?php echo e($key); ?>': <?php echo e(old('hims_features.' . $key, !empty($currentHimsFeatures[$key]) ? '1' : '') ? 'true' : 'false'); ?>,
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
                            <option value="<?php echo e($value); ?>" <?php echo e(old('facility_type', $clinic->facility_type ?? 'clinic') === $value ? 'selected' : ''); ?>><?php echo e($meta['label']); ?></option>
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
                    <input type="number" name="licensed_beds" min="0" value="<?php echo e(old('licensed_beds', $clinic->licensed_beds)); ?>"
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
                <input type="text" readonly
                    value="<?php echo e($clinic->slug); ?>.clinic0s.com"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed">
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

        
        <?php if($clinic->owner): ?>
        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Owner Information</h3>
            <p class="text-sm text-gray-500 mb-4">To edit owner details, go to Users management.</p>
            
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                    <span class="text-indigo-600 font-semibold"><?php echo e(substr($clinic->owner->name, 0, 1)); ?></span>
                </div>
                <div>
                    <p class="font-medium text-gray-900"><?php echo e($clinic->owner->name); ?></p>
                    <p class="text-sm text-gray-500"><?php echo e($clinic->owner->email); ?></p>
                </div>
                <a href="<?php echo e(route('admin.users.edit', $clinic->owner)); ?>" class="ml-auto px-4 py-2 text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                    Edit Owner →
                </a>
            </div>
        </div>
        <?php endif; ?>

        
        <div class="flex items-center justify-between">
            <a href="<?php echo e(route('admin.clinics.show', $clinic)); ?>" class="px-6 py-2.5 text-gray-700 hover:text-gray-900">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors">
                Save Changes
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/admin/clinics/edit.blade.php ENDPATH**/ ?>