<?php $__env->startSection('title', 'Photo Comparison - ' . $patient->name); ?>
<?php $__env->startSection('breadcrumb', 'Photo Comparison'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 space-y-6" x-data="photoComparison()">
    
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="<?php echo e(route('photo-vault.index')); ?>" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Before/After Comparison</h1>
                <p class="text-sm text-gray-500 mt-0.5"><?php echo e($patient->name); ?> · Patient ID: <?php echo e($patient->patient_id); ?></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('patients.show', $patient)); ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                View Profile
            </a>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="viewMode = 'sideBySide'" :class="viewMode === 'sideBySide' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors">
                    Side by Side
                </button>
                <button @click="viewMode = 'slider'" :class="viewMode === 'slider' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors">
                    Slider Comparison
                </button>
                <button @click="viewMode = 'timeline'" :class="viewMode === 'timeline' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors">
                    Timeline View
                </button>
                <button @click="viewMode = 'bodyMap'" :class="viewMode === 'bodyMap' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors">
                    Body Map
                </button>
            </nav>
        </div>

        
        <div x-show="viewMode === 'sideBySide'" class="p-6">
            <div class="grid grid-cols-2 gap-6">
                
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        Before
                    </h3>
                    <?php if($beforePhotos->count() > 0): ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $beforePhotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-gray-100 rounded-xl overflow-hidden cursor-pointer hover:ring-2 hover:ring-amber-500" @click="selectPhoto('before', <?php echo e($photo->id); ?>)">
                            <img src="<?php echo e(route('patients.view-photo', ['patient' => $patient->id, 'photo' => $photo->id])); ?>" 
                                 alt="Before photo" 
                                 class="w-full aspect-[4/3] object-cover">
                            <div class="p-3 bg-white">
                                <p class="text-sm font-medium text-gray-900"><?php echo e($photo->body_region ?? 'Unspecified region'); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($photo->created_at->format('d M Y')); ?></p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                    <div class="bg-gray-100 rounded-xl p-8 text-center">
                        <p class="text-gray-500">No "Before" photos uploaded</p>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        After
                    </h3>
                    <?php if($afterPhotos->count() > 0): ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $afterPhotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-gray-100 rounded-xl overflow-hidden cursor-pointer hover:ring-2 hover:ring-green-500" @click="selectPhoto('after', <?php echo e($photo->id); ?>)">
                            <img src="<?php echo e(route('patients.view-photo', ['patient' => $patient->id, 'photo' => $photo->id])); ?>" 
                                 alt="After photo" 
                                 class="w-full aspect-[4/3] object-cover">
                            <div class="p-3 bg-white">
                                <p class="text-sm font-medium text-gray-900"><?php echo e($photo->body_region ?? 'Unspecified region'); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($photo->created_at->format('d M Y')); ?></p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                    <div class="bg-gray-100 rounded-xl p-8 text-center">
                        <p class="text-gray-500">No "After" photos uploaded</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div x-show="viewMode === 'slider'" class="p-6">
            <div class="max-w-3xl mx-auto">
                <div class="mb-4 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Before Photo</label>
                        <select x-model="sliderBeforeId" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">Select before photo</option>
                            <?php $__currentLoopData = $beforePhotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($photo->id); ?>"><?php echo e($photo->body_region ?? 'No region'); ?> - <?php echo e($photo->created_at->format('d M Y')); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">After Photo</label>
                        <select x-model="sliderAfterId" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">Select after photo</option>
                            <?php $__currentLoopData = $afterPhotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($photo->id); ?>"><?php echo e($photo->body_region ?? 'No region'); ?> - <?php echo e($photo->created_at->format('d M Y')); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="relative aspect-[4/3] bg-gray-900 rounded-xl overflow-hidden" x-show="sliderBeforeId && sliderAfterId">
                    
                    <img :src="'/patients/' + <?php echo e($patient->id); ?> + '/photos/' + sliderAfterId" 
                         alt="After" 
                         class="absolute inset-0 w-full h-full object-cover"
                         x-show="sliderAfterId">
                    
                    
                    <div class="absolute inset-0 overflow-hidden" :style="'clip-path: inset(0 ' + (100 - sliderPosition) + '% 0 0)'">
                        <img :src="'/patients/' + <?php echo e($patient->id); ?> + '/photos/' + sliderBeforeId" 
                             alt="Before" 
                             class="absolute inset-0 w-full h-full object-cover"
                             x-show="sliderBeforeId">
                    </div>
                    
                    
                    <div class="absolute inset-y-0 w-1 bg-white shadow-lg cursor-ew-resize" 
                         :style="'left: ' + sliderPosition + '%'"
                         @mousedown="isDragging = true"
                         @touchstart="isDragging = true">
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-8 h-8 bg-white rounded-full shadow-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                            </svg>
                        </div>
                    </div>
                    
                    
                    <div class="absolute top-4 left-4 px-2 py-1 bg-amber-500 text-white text-xs font-bold rounded">BEFORE</div>
                    <div class="absolute top-4 right-4 px-2 py-1 bg-green-500 text-white text-xs font-bold rounded">AFTER</div>
                </div>

                <div class="mt-4" x-show="sliderBeforeId && sliderAfterId">
                    <input type="range" min="0" max="100" x-model="sliderPosition" class="w-full">
                </div>

                <div x-show="!sliderBeforeId || !sliderAfterId" class="aspect-[4/3] bg-gray-100 rounded-xl flex items-center justify-center">
                    <p class="text-gray-500">Select both before and after photos to compare</p>
                </div>
            </div>
        </div>

        
        <div x-show="viewMode === 'timeline'" class="p-6">
            <div class="relative">
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                
                <div class="space-y-8">
                    <?php $__currentLoopData = $photos->sortBy('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="relative pl-16">
                        <div class="absolute left-4 w-5 h-5 rounded-full border-4 border-white shadow
                            <?php if($photo->photo_type === 'before'): ?> bg-amber-500
                            <?php elseif($photo->photo_type === 'after'): ?> bg-green-500
                            <?php elseif($photo->photo_type === 'progress'): ?> bg-blue-500
                            <?php else: ?> bg-gray-400
                            <?php endif; ?>
                        "></div>
                        
                        <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors">
                            <div class="flex gap-4">
                                <img src="<?php echo e(route('patients.view-photo', ['patient' => $patient->id, 'photo' => $photo->id])); ?>" 
                                     alt="<?php echo e($photo->body_region ?? 'Photo'); ?>" 
                                     class="w-24 h-24 object-cover rounded-lg flex-shrink-0">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-semibold text-gray-900"><?php echo e($photo->created_at->format('d M Y')); ?></span>
                                        <span class="text-xs text-gray-400"><?php echo e($photo->created_at->format('h:i A')); ?></span>
                                    </div>
                                    <p class="text-sm text-gray-600"><?php echo e($photo->body_region ?? 'No region specified'); ?></p>
                                    <?php if($photo->photo_type): ?>
                                    <span class="inline-flex mt-2 px-2 py-0.5 text-xs font-medium rounded-full
                                        <?php if($photo->photo_type === 'before'): ?> bg-amber-100 text-amber-700
                                        <?php elseif($photo->photo_type === 'after'): ?> bg-green-100 text-green-700
                                        <?php elseif($photo->photo_type === 'progress'): ?> bg-blue-100 text-blue-700
                                        <?php else: ?> bg-gray-100 text-gray-700
                                        <?php endif; ?>
                                    "><?php echo e(ucfirst($photo->photo_type)); ?></span>
                                    <?php endif; ?>
                                    <?php if($photo->description): ?>
                                    <p class="text-xs text-gray-500 mt-2"><?php echo e($photo->description); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        
        <div x-show="viewMode === 'bodyMap'" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-1">
                    <div class="bg-gray-100 rounded-xl p-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Photo Locations</h4>
                        
                        <div class="relative aspect-[3/4] bg-white rounded-lg overflow-hidden">
                            
                            <svg viewBox="0 0 200 300" class="w-full h-full">
                                
                                <g fill="none" stroke="#d1d5db" stroke-width="2">
                                    
                                    <ellipse cx="100" cy="30" rx="20" ry="25" class="body-part" data-region="face" :class="{'fill-blue-200 stroke-blue-500': selectedRegion === 'face'}" @click="selectedRegion = 'face'"/>
                                    
                                    <rect x="92" y="52" width="16" height="15" class="body-part" data-region="neck" :class="{'fill-blue-200 stroke-blue-500': selectedRegion === 'neck'}" @click="selectedRegion = 'neck'"/>
                                    
                                    <rect x="65" y="65" width="70" height="80" rx="10" class="body-part" data-region="torso" :class="{'fill-blue-200 stroke-blue-500': selectedRegion === 'torso'}" @click="selectedRegion = 'torso'"/>
                                    
                                    <rect x="30" y="70" width="30" height="70" rx="5" class="body-part" data-region="upper_limbs" :class="{'fill-blue-200 stroke-blue-500': selectedRegion === 'upper_limbs'}" @click="selectedRegion = 'upper_limbs'"/>
                                    
                                    <rect x="140" y="70" width="30" height="70" rx="5" class="body-part" data-region="upper_limbs" :class="{'fill-blue-200 stroke-blue-500': selectedRegion === 'upper_limbs'}" @click="selectedRegion = 'upper_limbs'"/>
                                    
                                    <rect x="65" y="150" width="30" height="100" rx="5" class="body-part" data-region="lower_limbs" :class="{'fill-blue-200 stroke-blue-500': selectedRegion === 'lower_limbs'}" @click="selectedRegion = 'lower_limbs'"/>
                                    
                                    <rect x="105" y="150" width="30" height="100" rx="5" class="body-part" data-region="lower_limbs" :class="{'fill-blue-200 stroke-blue-500': selectedRegion === 'lower_limbs'}" @click="selectedRegion = 'lower_limbs'"/>
                                </g>
                                
                                
                                <?php $__currentLoopData = $groupedByRegion; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region => $regionPhotos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $positions = [
                                        'face' => ['cx' => 120, 'cy' => 30],
                                        'Full Face' => ['cx' => 120, 'cy' => 30],
                                        'neck' => ['cx' => 130, 'cy' => 60],
                                        'Chest' => ['cx' => 100, 'cy' => 90],
                                        'Upper Back' => ['cx' => 100, 'cy' => 90],
                                        'Abdomen' => ['cx' => 100, 'cy' => 120],
                                        'Hand' => ['cx' => 30, 'cy' => 130],
                                        'Fingers' => ['cx' => 30, 'cy' => 140],
                                        'Foot' => ['cx' => 80, 'cy' => 250],
                                    ];
                                    $pos = $positions[$region] ?? ['cx' => 100, 'cy' => 150];
                                ?>
                                <g>
                                    <circle cx="<?php echo e($pos['cx']); ?>" cy="<?php echo e($pos['cy']); ?>" r="8" fill="#3b82f6"/>
                                    <text x="<?php echo e($pos['cx']); ?>" y="<?php echo e($pos['cy'] + 3); ?>" fill="white" font-size="8" text-anchor="middle"><?php echo e($regionPhotos->count()); ?></text>
                                </g>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </svg>
                        </div>
                        
                        <p class="text-xs text-gray-500 text-center mt-3">Click a body part to filter photos</p>
                    </div>
                </div>

                
                <div class="lg:col-span-2">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Photos by Body Region</h4>
                    
                    <?php $__empty_1 = true; $__currentLoopData = $groupedByRegion; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region => $regionPhotos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="mb-6" x-show="!selectedRegion || selectedRegion === '<?php echo e(strtolower(str_replace(' ', '_', $region))); ?>'">
                        <h5 class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <?php echo e($region ?? 'Unspecified'); ?>

                            <span class="text-xs text-gray-400">(<?php echo e($regionPhotos->count()); ?> photos)</span>
                        </h5>
                        <div class="grid grid-cols-3 gap-3">
                            <?php $__currentLoopData = $regionPhotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-gray-100 rounded-lg overflow-hidden group cursor-pointer">
                                <img src="<?php echo e(route('patients.view-photo', ['patient' => $patient->id, 'photo' => $photo->id])); ?>" 
                                     alt="<?php echo e($region); ?>" 
                                     class="w-full aspect-square object-cover group-hover:scale-105 transition-transform">
                                <div class="p-2 bg-white">
                                    <p class="text-xs text-gray-500"><?php echo e($photo->created_at->format('d M Y')); ?></p>
                                    <?php if($photo->photo_type): ?>
                                    <span class="inline-flex mt-1 px-1.5 py-0.5 text-[10px] font-medium rounded
                                        <?php if($photo->photo_type === 'before'): ?> bg-amber-100 text-amber-700
                                        <?php elseif($photo->photo_type === 'after'): ?> bg-green-100 text-green-700
                                        <?php else: ?> bg-gray-100 text-gray-700
                                        <?php endif; ?>
                                    "><?php echo e(ucfirst($photo->photo_type)); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="bg-gray-100 rounded-xl p-8 text-center">
                        <p class="text-gray-500">No photos with body region tags</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <?php if (isset($component)) { $__componentOriginalad562e6dc0527dcea6ea8e1d5cb262a2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalad562e6dc0527dcea6ea8e1d5cb262a2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.photo-consent-signature','data' => ['patientId' => $patient->id,'title' => 'Patient consent signature (recommended)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('photo-consent-signature'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['patient-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($patient->id),'title' => 'Patient consent signature (recommended)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalad562e6dc0527dcea6ea8e1d5cb262a2)): ?>
<?php $attributes = $__attributesOriginalad562e6dc0527dcea6ea8e1d5cb262a2; ?>
<?php unset($__attributesOriginalad562e6dc0527dcea6ea8e1d5cb262a2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalad562e6dc0527dcea6ea8e1d5cb262a2)): ?>
<?php $component = $__componentOriginalad562e6dc0527dcea6ea8e1d5cb262a2; ?>
<?php unset($__componentOriginalad562e6dc0527dcea6ea8e1d5cb262a2); ?>
<?php endif; ?>

    
    <div class="bg-white rounded-xl border border-gray-200 p-6" x-data="photoVaultUploadForm(<?php echo \Illuminate\Support\Js::from(route('photo-vault.upload'))->toHtml() ?>)">
        <h3 class="font-semibold text-gray-900 mb-4">Upload New Photo</h3>
        <p class="text-xs text-gray-500 mb-4">Use the signature pad above to record consent, then confirm below and upload.</p>

        <form enctype="multipart/form-data" class="space-y-4" @submit.prevent="submitUpload($event)">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="patient_id" value="<?php echo e($patient->id); ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo Type</label>
                    <select name="photo_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="before">Before</option>
                        <option value="after">After</option>
                        <option value="progress">Progress</option>
                        <option value="clinical">Clinical</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Body Region</label>
                    <select name="body_region" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Select region</option>
                        <option value="Face">Face</option>
                        <option value="Scalp">Scalp</option>
                        <option value="Neck">Neck</option>
                        <option value="Chest">Chest</option>
                        <option value="Back">Back</option>
                        <option value="Abdomen">Abdomen</option>
                        <option value="Upper Arm">Upper Arm</option>
                        <option value="Forearm">Forearm</option>
                        <option value="Hand">Hand</option>
                        <option value="Thigh">Thigh</option>
                        <option value="Leg">Leg</option>
                        <option value="Foot">Foot</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                    <input type="file" name="photo" accept="image/*" required 
                           @change="preview = URL.createObjectURL($event.target.files[0])"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Add notes about this photo..."></textarea>
            </div>
            
            <div class="flex items-start gap-3 p-4 bg-amber-50 rounded-xl border border-amber-100">
                <input type="checkbox" name="consent_confirmed" id="pv_consent_confirmed" value="1" required
                       class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="pv_consent_confirmed" class="text-sm text-gray-700">
                    I confirm that valid consent was obtained for this clinical photograph (signature above when possible).
                </label>
            </div>

            <div class="flex items-center gap-4">
                <img :src="preview" x-show="preview" class="w-20 h-20 object-cover rounded-lg">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
                        :disabled="uploading">
                    <span x-show="!uploading">Upload Photo</span>
                    <span x-show="uploading">Uploading…</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
console.log('Photo comparison page loaded');

function photoVaultUploadForm(uploadUrl) {
    return {
        uploadUrl,
        uploading: false,
        preview: null,
        async submitUpload(event) {
            const form = event.target;
            const fd = new FormData(form);
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            console.log('[photoVaultUploadForm] submit', { uploadUrl: this.uploadUrl, keys: [...fd.keys()] });
            this.uploading = true;
            try {
                const res = await fetch(this.uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: fd,
                    credentials: 'same-origin',
                });
                const data = await res.json().catch(() => ({}));
                console.log('[photoVaultUploadForm] response', { status: res.status, data });
                if (!res.ok) {
                    throw new Error(data.error || data.message || ('Upload failed: ' + res.status));
                }
                window.location.reload();
            } catch (e) {
                console.error('[photoVaultUploadForm]', e);
                alert(e.message || 'Upload failed');
            } finally {
                this.uploading = false;
            }
        },
    };
}

function photoComparison() {
    return {
        viewMode: 'sideBySide',
        selectedRegion: null,
        sliderPosition: 50,
        sliderBeforeId: null,
        sliderAfterId: null,
        isDragging: false,

        init() {
            console.log('Photo comparison initialized');
            
            document.addEventListener('mousemove', (e) => {
                if (this.isDragging) {
                    const container = document.querySelector('.aspect-\\[4\\/3\\]');
                    if (container) {
                        const rect = container.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        this.sliderPosition = Math.max(0, Math.min(100, (x / rect.width) * 100));
                    }
                }
            });

            document.addEventListener('mouseup', () => {
                this.isDragging = false;
            });
        },

        selectPhoto(type, photoId) {
            console.log('Selected photo:', type, photoId);
            if (type === 'before') {
                this.sliderBeforeId = photoId;
            } else {
                this.sliderAfterId = photoId;
            }
            this.viewMode = 'slider';
        }
    };
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/photo-vault/comparison.blade.php ENDPATH**/ ?>