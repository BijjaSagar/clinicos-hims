<?php $__env->startSection('title', 'Insurance & TPA'); ?>

<?php $__env->startSection('breadcrumb', 'Insurance Claims'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="insuranceDashboard()" class="p-6 space-y-6">
    <?php if(isset($insuranceSchemaReady) && !$insuranceSchemaReady): ?>
    <div class="rounded-xl border border-amber-200 bg-amber-50 text-amber-900 px-4 py-3 text-sm">
        Insurance tables are not installed yet. Run <code class="bg-amber-100 px-1 rounded">php artisan migrate</code> to enable claims and pre-auth.
    </div>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-2xl">⏳</div>
                <div>
                    <div class="text-2xl font-bold text-gray-900"><?php echo e($stats['pending_claims']); ?></div>
                    <div class="text-sm text-gray-500">Pending Claims</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-2xl">✅</div>
                <div>
                    <div class="text-2xl font-bold text-gray-900"><?php echo e($stats['approved_claims']); ?></div>
                    <div class="text-sm text-gray-500">Approved (This Month)</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-2xl">📝</div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">₹<?php echo e(number_format($stats['total_claimed_amount'])); ?></div>
                    <div class="text-sm text-gray-500">Total Claimed</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-2xl">💰</div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">₹<?php echo e(number_format($stats['total_settled_amount'])); ?></div>
                    <div class="text-sm text-gray-500">Total Settled</div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200 px-4">
            <nav class="flex gap-4">
                <button @click="activeTab = 'claims'" :class="activeTab === 'claims' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500'" class="px-4 py-4 text-sm font-medium border-b-2 transition-colors">
                    Insurance Claims
                </button>
                <button @click="activeTab = 'preauth'" :class="activeTab === 'preauth' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500'" class="px-4 py-4 text-sm font-medium border-b-2 transition-colors">
                    Pre-Authorization
                </button>
                <button @click="activeTab = 'tpa'" :class="activeTab === 'tpa' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500'" class="px-4 py-4 text-sm font-medium border-b-2 transition-colors">
                    TPA Configuration
                </button>
            </nav>
        </div>

        
        <div x-show="activeTab === 'claims'" class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-semibold text-gray-900">Recent Claims</h3>
                <button @click="showNewClaimModal = true" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Claim
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Claim #</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Patient</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Insurance</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $recentClaims; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $claim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm font-medium text-blue-600"><?php echo e($claim->claim_number); ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900"><?php echo e($claim->patient_name); ?></div>
                                <div class="text-xs text-gray-500"><?php echo e($claim->member_id); ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900"><?php echo e($claim->insurance_company); ?></div>
                                <div class="text-xs text-gray-500"><?php echo e($claim->policy_number); ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">₹<?php echo e(number_format($claim->claim_amount)); ?></div>
                                <?php if($claim->settled_amount): ?>
                                <div class="text-xs text-green-600">Settled: ₹<?php echo e(number_format($claim->settled_amount)); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php
                                    $statusColors = [
                                        'pending' => 'bg-gray-100 text-gray-700',
                                        'submitted' => 'bg-blue-100 text-blue-700',
                                        'under_process' => 'bg-yellow-100 text-yellow-700',
                                        'query' => 'bg-orange-100 text-orange-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        'settled' => 'bg-purple-100 text-purple-700',
                                    ];
                                ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo e($statusColors[$claim->status] ?? 'bg-gray-100'); ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $claim->status))); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <?php echo e(\Carbon\Carbon::parse($claim->created_at)->format('d M Y')); ?>

                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button @click="viewClaim(<?php echo e(json_encode($claim)); ?>)" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button @click="updateClaimStatus(<?php echo e($claim->id); ?>)" class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                No claims found. Create your first claim.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div x-show="activeTab === 'preauth'" class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-semibold text-gray-900">Pre-Authorization Requests</h3>
                <button @click="showPreAuthModal = true" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Request Pre-Auth
                </button>
            </div>

            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $pendingPreAuths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $preauth): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-3">
                                <h4 class="font-semibold text-gray-900"><?php echo e($preauth->patient_name); ?></h4>
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full <?php echo e($preauth->admission_type === 'emergency' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'); ?>">
                                    <?php echo e(ucfirst($preauth->admission_type)); ?>

                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1"><?php echo e($preauth->insurance_company); ?> • <?php echo e($preauth->policy_number); ?></p>
                            <p class="text-sm text-gray-500 mt-1">Admission: <?php echo e(\Carbon\Carbon::parse($preauth->admission_date)->format('d M Y')); ?></p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-900">₹<?php echo e(number_format($preauth->estimated_amount)); ?></div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo e($preauth->status === 'approved' ? 'bg-green-100 text-green-700' : ($preauth->status === 'query' ? 'bg-orange-100 text-orange-700' : 'bg-yellow-100 text-yellow-700')); ?>">
                                <?php echo e(ucfirst($preauth->status)); ?>

                            </span>
                        </div>
                    </div>
                    <?php if($preauth->query_details): ?>
                    <div class="mt-3 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                        <p class="text-sm text-orange-800"><strong>Query:</strong> <?php echo e($preauth->query_details); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-12 text-gray-500">
                    No pending pre-authorization requests.
                </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div x-show="activeTab === 'tpa'" class="p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h3 class="font-semibold text-gray-900">TPA configurations</h3>
                    <p class="text-sm text-gray-500 mt-1">Save empanelment and portal details for each TPA you work with. Pre-auth uses the TPA code you store here.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="applyParamountTemplate()" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        Paramount TPA (PARAM)
                    </button>
                    <button type="button" @click="openTpaModal()" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700">
                        Add / edit TPA
                    </button>
                </div>
            </div>

            <?php if(isset($insuranceSchemaReady) && !$insuranceSchemaReady): ?>
            <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">Run migrations to enable TPA storage.</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = ($tpaConfigs ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cfg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="border border-gray-200 rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-xs"><?php echo e(strtoupper(substr($cfg->tpa_code, 0, 2))); ?></div>
                        <div>
                            <h4 class="font-semibold text-gray-900"><?php echo e($cfg->tpa_name); ?></h4>
                            <p class="text-xs text-gray-500">Code <span class="font-mono"><?php echo e($cfg->tpa_code); ?></span>
                                <?php if($cfg->empanelment_id): ?> · Empanelment <?php echo e($cfg->empanelment_id); ?> <?php endif; ?>
                            </p>
                            <?php if($cfg->portal_url): ?>
                            <a href="<?php echo e($cfg->portal_url); ?>" target="_blank" rel="noopener" class="text-xs text-blue-600 hover:underline">TPA portal</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button type="button" @click="deleteTpaConfig(<?php echo e($cfg->id); ?>)" class="text-sm text-red-600 hover:text-red-800 font-medium self-start sm:self-center">Remove</button>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-10 text-gray-500 border border-dashed border-gray-200 rounded-xl">
                    <p class="mb-2">No TPA saved yet.</p>
                    <p class="text-sm">Click <strong>Paramount TPA (PARAM)</strong> for a ready-made template, then add your empanelment ID and portal login.</p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div x-show="showTPAModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" @keydown.escape.window="showTPAModal = false">
        <div @click.outside="showTPAModal = false" class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">TPA details</h4>
            <form class="space-y-3" @submit.prevent="saveTpaConfig()">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">TPA code *</label>
                        <input type="text" x-model="tpaForm.tpa_code" required maxlength="20" class="w-full mt-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono uppercase">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">TPA name *</label>
                        <input type="text" x-model="tpaForm.tpa_name" required maxlength="200" class="w-full mt-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Empanelment ID</label>
                        <input type="text" x-model="tpaForm.empanelment_id" class="w-full mt-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Provider / hospital ID</label>
                        <input type="text" x-model="tpaForm.provider_id" class="w-full mt-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Rohini ID</label>
                    <input type="text" x-model="tpaForm.rohini_id" class="w-full mt-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Portal URL</label>
                    <input type="url" x-model="tpaForm.portal_url" class="w-full mt-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="https://">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Portal username</label>
                        <input type="text" x-model="tpaForm.portal_username" class="w-full mt-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Portal password</label>
                        <input type="password" x-model="tpaForm.portal_password" class="w-full mt-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Leave blank to keep existing">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Contact email</label>
                        <input type="email" x-model="tpaForm.contact_email" class="w-full mt-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Contact phone</label>
                        <input type="text" x-model="tpaForm.contact_phone" class="w-full mt-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" x-model="tpaForm.is_active" class="rounded border-gray-300">
                    Active (show in pre-auth dropdown)
                </label>
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="showTPAModal = false" class="flex-1 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700">Cancel</button>
                    <button type="submit" class="flex-1 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
console.log('Insurance dashboard loaded');

function insuranceDashboard() {
    return {
        activeTab: 'claims',
        showNewClaimModal: false,
        showPreAuthModal: false,
        showTPAModal: false,
        tpaForm: {
            tpa_code: '',
            tpa_name: '',
            empanelment_id: '',
            provider_id: '',
            rohini_id: '',
            portal_url: '',
            portal_username: '',
            portal_password: '',
            contact_email: '',
            contact_phone: '',
            is_active: true,
        },

        openTpaModal() {
            this.tpaForm = {
                tpa_code: '',
                tpa_name: '',
                empanelment_id: '',
                provider_id: '',
                rohini_id: '',
                portal_url: '',
                portal_username: '',
                portal_password: '',
                contact_email: '',
                contact_phone: '',
                is_active: true,
            };
            this.showTPAModal = true;
            console.log('[insurance] TPA modal opened (new)');
        },

        applyParamountTemplate() {
            this.tpaForm = {
                tpa_code: 'PARAM',
                tpa_name: 'Paramount TPA',
                empanelment_id: '',
                provider_id: '',
                rohini_id: '',
                portal_url: 'https://www.paramounttpa.com/',
                portal_username: '',
                portal_password: '',
                contact_email: '',
                contact_phone: '',
                is_active: true,
            };
            this.showTPAModal = true;
            console.log('[insurance] Paramount TPA template loaded');
        },

        async saveTpaConfig() {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const body = {
                tpa_code: this.tpaForm.tpa_code,
                tpa_name: this.tpaForm.tpa_name,
                empanelment_id: this.tpaForm.empanelment_id || null,
                provider_id: this.tpaForm.provider_id || null,
                rohini_id: this.tpaForm.rohini_id || null,
                portal_url: this.tpaForm.portal_url || null,
                portal_username: this.tpaForm.portal_username || null,
                portal_password: this.tpaForm.portal_password || null,
                contact_email: this.tpaForm.contact_email || null,
                contact_phone: this.tpaForm.contact_phone || null,
                is_active: !!this.tpaForm.is_active,
            };
            console.log('[insurance] saving TPA config', { code: body.tpa_code });
            try {
                const res = await fetch(<?php echo json_encode(route('insurance.tpa.store'), 15, 512) ?>, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(body),
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    alert(data.message || 'Saved.');
                    window.location.reload();
                } else {
                    alert(data.error || 'Could not save TPA.');
                }
            } catch (e) {
                console.error('[insurance] TPA save error', e);
                alert('Network error.');
            }
        },

        async deleteTpaConfig(id) {
            if (!confirm('Remove this TPA configuration?')) return;
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            console.log('[insurance] delete TPA', id);
            try {
                const res = await fetch('/insurance/tpa-config/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    window.location.reload();
                } else {
                    alert(data.error || 'Could not remove.');
                }
            } catch (e) {
                console.error('[insurance] TPA delete error', e);
            }
        },

        viewClaim(claim) {
            console.log('Viewing claim:', claim);
            alert('Claim Details:\n\nClaim #: ' + claim.claim_number + '\nPatient: ' + claim.patient_name + '\nAmount: ₹' + claim.claim_amount + '\nStatus: ' + claim.status);
        },

        updateClaimStatus(claimId) {
            const newStatus = prompt('Enter new status (pending, submitted, under_process, query, approved, rejected, settled):');
            if (newStatus) {
                fetch('/insurance/claims/' + claimId + '/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ status: newStatus }),
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        alert('Status updated!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.error);
                    }
                });
            }
        },
    };
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/insurance/index.blade.php ENDPATH**/ ?>