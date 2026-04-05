@extends('layouts.app')
@section('title', 'Smart Scan - Lab Report OCR')
@section('content')
<div class="container-fluid py-4">
    <h4 class="mb-4"><i class="fas fa-qrcode me-2"></i>Smart Scan — Lab Report Reader</h4>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0">Upload Lab Report</h6></div>
                <div class="card-body" x-data="smartScan()">
                    <form @submit.prevent="uploadFile">
                        <div class="mb-3">
                            <label class="form-label">Select Image or PDF</label>
                            <input type="file" class="form-control" accept="image/*,.pdf" @change="handleFile($event)" required>
                            <small class="text-muted">JPG, PNG, or PDF. Max 10MB.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" :disabled="uploading">
                            <span x-show="!uploading"><i class="fas fa-upload me-1"></i>Upload & Scan</span>
                            <span x-show="uploading"><i class="fas fa-spinner fa-spin me-1"></i>Processing...</span>
                        </button>
                    </form>

                    <div x-show="message" class="alert mt-3" :class="success ? 'alert-success' : 'alert-danger'" x-text="message"></div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white"><h6 class="mb-0">Paste Lab Report Text</h6></div>
                <div class="card-body" x-data="textParser()">
                    <textarea class="form-control mb-2" rows="6" x-model="rawText" placeholder="Paste lab report text here...&#10;&#10;Hemoglobin: 12.5 g/dL&#10;WBC: 8500 /cumm&#10;Creatinine: 0.9 mg/dL"></textarea>
                    <button class="btn btn-outline-primary w-100" @click="parseText" :disabled="parsing">
                        <i class="fas fa-search me-1"></i>Extract Lab Values
                    </button>

                    <div x-show="parsedValues.length > 0" class="mt-3">
                        <h6>Detected Values:</h6>
                        <table class="table table-sm">
                            <thead><tr><th>Test</th><th>Value</th><th>Unit</th><th>Flag</th></tr></thead>
                            <tbody>
                            <template x-for="val in parsedValues" :key="val.test">
                                <tr>
                                    <td x-text="val.label"></td>
                                    <td class="fw-bold" x-text="val.value"></td>
                                    <td x-text="val.unit"></td>
                                    <td>
                                        <span class="badge" :class="val.flag === 'high' ? 'bg-danger' : (val.flag === 'low' ? 'bg-warning' : 'bg-success')" x-text="val.flag.toUpperCase()"></span>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0">How It Works</h6></div>
                <div class="card-body">
                    <div class="mb-3 p-3 bg-light rounded">
                        <strong>Step 1:</strong> Upload a lab report image or PDF<br>
                        <strong>Step 2:</strong> AI extracts lab values automatically<br>
                        <strong>Step 3:</strong> Review and save to patient record
                    </div>
                    <h6>Supported Tests</h6>
                    <div class="row g-2">
                        @foreach(['Hemoglobin', 'WBC', 'RBC', 'Platelets', 'HbA1c', 'Blood Sugar', 'Creatinine', 'BUN', 'SGPT/ALT', 'SGOT/AST', 'Cholesterol', 'HDL', 'LDL', 'Triglycerides', 'TSH', 'T3/T4', 'Vitamin D', 'Vitamin B12', 'Calcium', 'Sodium', 'Potassium', 'Uric Acid', 'ESR', 'CRP'] as $test)
                        <div class="col-6"><span class="badge bg-light text-dark border w-100 py-1">{{ $test }}</span></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
console.log('Smart Scan page loaded');

function smartScan() {
    return {
        file: null, uploading: false, message: '', success: false,
        handleFile(e) { this.file = e.target.files[0]; console.log('File selected', this.file?.name); },
        async uploadFile() {
            if (!this.file) return;
            this.uploading = true; this.message = '';
            const fd = new FormData();
            fd.append('file', this.file);
            fd.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
            try {
                const resp = await fetch('{{ route("smart-scan.upload") }}', { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' } });
                const data = await resp.json();
                this.message = data.message; this.success = data.success;
                console.log('Upload result', data);
            } catch (err) { this.message = 'Upload failed'; this.success = false; console.error(err); }
            this.uploading = false;
        }
    };
}

function textParser() {
    return {
        rawText: '', parsedValues: [], parsing: false,
        async parseText() {
            if (!this.rawText.trim()) return;
            this.parsing = true;
            try {
                const resp = await fetch('{{ route("smart-scan.parse") }}', {
                    method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                    body: JSON.stringify({ text: this.rawText })
                });
                const data = await resp.json();
                this.parsedValues = data.parsed_values || [];
                console.log('Parsed', this.parsedValues.length, 'values');
            } catch (err) { console.error('Parse error', err); }
            this.parsing = false;
        }
    };
}
</script>
@endsection
