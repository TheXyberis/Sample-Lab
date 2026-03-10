@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <h3 class="text-primary mb-4">Sample Registration (Wizard)</h3>

    <div class="stepper mb-4">
        <div class="d-flex justify-content-between">
            <div class="d-flex flex-column align-items-center stepper-item active" data-step="1">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center step-circle" style="width: 40px; height: 40px;">1</div>
                <small class="mt-1 text-muted step-label">Client & Project</small>
            </div>
            <div class="flex-grow-1 border-top align-self-center mx-2" style="margin-top: -25px !important;"></div>
            <div class="d-flex flex-column align-items-center stepper-item" data-step="2">
                <div class="rounded-circle border border-secondary text-secondary d-flex align-items-center justify-content-center step-circle" style="width: 40px; height: 40px;">2</div>
                <small class="mt-1 text-muted step-label">Name & Type</small>
            </div>
            <div class="flex-grow-1 border-top align-self-center mx-2" style="margin-top: -25px !important;"></div>
            <div class="d-flex flex-column align-items-center stepper-item" data-step="3">
                <div class="rounded-circle border border-secondary text-secondary d-flex align-items-center justify-content-center step-circle" style="width: 40px; height: 40px;">3</div>
                <small class="mt-1 text-muted step-label">Quantity & Unit</small>
            </div>
            <div class="flex-grow-1 border-top align-self-center mx-2" style="margin-top: -25px !important;"></div>
            <div class="d-flex flex-column align-items-center stepper-item" data-step="4">
                <div class="rounded-circle border border-secondary text-secondary d-flex align-items-center justify-content-center step-circle" style="width: 40px; height: 40px;">4</div>
                <small class="mt-1 text-muted step-label">Review</small>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div id="validation-errors" class="alert alert-danger d-none"></div>

            <div id="step-1" class="wizard-step">
                <h5 class="mb-3">Step 1: Client and Project</h5>
                <div class="mb-3">
                    <label class="form-label">Client</label>
                    <select name="client_id" class="form-select">
                        <option value="">Select client</option>
                        @foreach(App\Models\Client::all() as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select">
                        <option value="">Select project</option>
                        @foreach(App\Models\Project::all() as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="step-2" class="wizard-step d-none">
                <h5 class="mb-3">Step 2: Name and Type</h5>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Sample name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <input type="text" name="type" class="form-control" placeholder="Sample type">
                </div>
            </div>

            <div id="step-3" class="wizard-step d-none">
                <h5 class="mb-3">Step 3: Quantity and Unit</h5>
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" placeholder="0" step="any" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">Unit</label>
                    <input type="text" name="unit" class="form-control" placeholder="e.g. ml, g">
                </div>
            </div>

            <div id="step-4" class="wizard-step d-none">
                <h5 class="mb-3">Step 4: Review</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th style="width: 35%;">Client</th><td id="review-client">-</td></tr>
                            <tr><th>Project</th><td id="review-project">-</td></tr>
                            <tr><th>Name</th><td id="review-name">-</td></tr>
                            <tr><th>Type</th><td id="review-type">-</td></tr>
                            <tr><th>Quantity</th><td id="review-quantity">-</td></tr>
                            <tr><th>Unit</th><td id="review-unit">-</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary" id="btn-prev" style="visibility: hidden;">Previous</button>
                <button type="button" class="btn btn-primary" id="btn-next">Next</button>
            </div>
        </div>
    </div>
</div>

<style>
.stepper .step-circle { font-weight: 600; }
.stepper .stepper-item.active .step-circle { background-color: #0d6efd !important; border: none !important; color: white !important; }
.stepper .stepper-item.completed .step-circle { background-color: #198754 !important; border: none !important; color: white !important; }
.stepper .stepper-item .step-label { font-size: 0.75rem; }
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalSteps = 4;
    let currentStep = 1;
    const btnNext = document.getElementById('btn-next');
    const btnPrev = document.getElementById('btn-prev');
    const errorsEl = document.getElementById('validation-errors');
    const validateUrl = '{{ route("samples.validate-step", ":step") }}';

    const clients = @json(App\Models\Client::all()->pluck('name', 'id'));
    const projects = @json(App\Models\Project::all()->pluck('name', 'id'));

    function showStep(step) {
        document.querySelectorAll('.wizard-step').forEach(el => el.classList.add('d-none'));
        const stepEl = document.getElementById('step-' + step);
        if (stepEl) stepEl.classList.remove('d-none');

        document.querySelectorAll('.stepper-item').forEach((el, i) => {
            el.classList.remove('active', 'completed');
            if (i + 1 < step) el.classList.add('completed');
            else if (i + 1 === step) el.classList.add('active');
        });

        btnPrev.style.visibility = step > 1 ? 'visible' : 'hidden';
        btnNext.textContent = step === totalSteps ? 'Submit' : 'Next';
        errorsEl.classList.add('d-none');
        currentStep = step;
    }

    function getStepData(step) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        if (step === 1) {
            formData.append('client_id', document.querySelector('#step-1 [name="client_id"]').value);
            formData.append('project_id', document.querySelector('#step-1 [name="project_id"]').value);
        } else if (step === 2) {
            formData.append('name', document.querySelector('#step-2 [name="name"]').value);
            formData.append('type', document.querySelector('#step-2 [name="type"]').value);
        } else if (step === 3) {
            formData.append('quantity', document.querySelector('#step-3 [name="quantity"]').value);
            formData.append('unit', document.querySelector('#step-3 [name="unit"]').value);
        } else if (step === 4) {
            formData.append('confirm', '1');
        }
        return formData;
    }

    function fillReview() {
        const cid = document.querySelector('[name="client_id"]').value;
        const pid = document.querySelector('[name="project_id"]').value;
        document.getElementById('review-client').textContent = cid ? (clients[cid] || '-') : '-';
        document.getElementById('review-project').textContent = pid ? (projects[pid] || '-') : '-';
        document.getElementById('review-name').textContent = document.querySelector('[name="name"]').value || '-';
        document.getElementById('review-type').textContent = document.querySelector('[name="type"]').value || '-';
        document.getElementById('review-quantity').textContent = document.querySelector('[name="quantity"]').value || '-';
        document.getElementById('review-unit').textContent = document.querySelector('[name="unit"]').value || '-';
    }

    btnNext.addEventListener('click', function() {
        if (currentStep === totalSteps) {
            return;
        }

        fetch(validateUrl.replace(':step', currentStep), {
            method: 'POST',
            body: getStepData(currentStep),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.valid) {
                if (currentStep === 3) fillReview();
                showStep(currentStep + 1);
            } else {
                const msgs = Object.values(data.errors || {}).flat().join('<br>');
                errorsEl.innerHTML = msgs;
                errorsEl.classList.remove('d-none');
            }
        })
        .catch(err => {
            errorsEl.innerHTML = 'Network error';
            errorsEl.classList.remove('d-none');
        });
    });

    btnPrev.addEventListener('click', function() {
        if (currentStep > 1) showStep(currentStep - 1);
    });
});
</script>
@endsection
@endsection
