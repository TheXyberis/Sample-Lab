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
                <small class="mt-1 text-muted step-label">Methods</small>
            </div>
            <div class="flex-grow-1 border-top align-self-center mx-2" style="margin-top: -25px !important;"></div>
            <div class="d-flex flex-column align-items-center stepper-item" data-step="5">
                <div class="rounded-circle border border-secondary text-secondary d-flex align-items-center justify-content-center step-circle" style="width: 40px; height: 40px;">5</div>
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
                    <div class="d-flex gap-2">
                        <select name="client_id" class="form-select flex-grow-1">
                            <option value="">Select client</option>
                            @foreach(App\Models\Client::all() as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary" id="btn-add-client">+ Add New</button>
                    </div>
                    <div id="new-client-form" class="d-none mt-2">
                        <div class="input-group">
                            <input type="text" id="new-client-name" class="form-control" placeholder="Enter new client name">
                            <button type="button" class="btn btn-success" id="btn-save-client">Save</button>
                            <button type="button" class="btn btn-secondary" id="btn-cancel-client">Cancel</button>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Project</label>
                    <div class="d-flex gap-2">
                        <select name="project_id" class="form-select flex-grow-1">
                            <option value="">Select project</option>
                            @foreach(App\Models\Project::all() as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary" id="btn-add-project">+ Add New</button>
                    </div>
                    <div id="new-project-form" class="d-none mt-2">
                        <div class="input-group">
                            <input type="text" id="new-project-name" class="form-control" placeholder="Enter new project name">
                            <button type="button" class="btn btn-success" id="btn-save-project">Save</button>
                            <button type="button" class="btn btn-secondary" id="btn-cancel-project">Cancel</button>
                        </div>
                    </div>
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
                <h5 class="mb-3">Step 4: Methods</h5>
                <p class="text-muted small mb-3">Select at least one method for this sample.</p>
                <div class="border rounded p-3 bg-light">
                    @forelse(App\Models\Method::all() as $method)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="method_ids[]" value="{{ $method->id }}" id="method-{{ $method->id }}">
                            <label class="form-check-label" for="method-{{ $method->id }}">
                                {{ $method->name }}@if($method->version) <span class="text-muted">(v{{ $method->version }})</span>@endif
                            </label>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No methods available.</p>
                    @endforelse
                </div>
            </div>

            <div id="step-5" class="wizard-step d-none">
                <h5 class="mb-3">Step 5: Review</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th style="width: 35%;">Client</th><td id="review-client">-</td></tr>
                            <tr><th>Project</th><td id="review-project">-</td></tr>
                            <tr><th>Name</th><td id="review-name">-</td></tr>
                            <tr><th>Type</th><td id="review-type">-</td></tr>
                            <tr><th>Quantity</th><td id="review-quantity">-</td></tr>
                            <tr><th>Unit</th><td id="review-unit">-</td></tr>
                            <tr><th>Methods</th><td id="review-methods">-</td></tr>
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
    const totalSteps = 5;
    let currentStep = 1;
    const btnNext = document.getElementById('btn-next');
    const btnPrev = document.getElementById('btn-prev');
    const errorsEl = document.getElementById('validation-errors');
    const validateUrl = '{{ route("samples.validate-step", ":step") }}';

    const clients = @json(App\Models\Client::all()->pluck('name', 'id'));
    const projects = @json(App\Models\Project::all()->pluck('name', 'id'));
    const methods = @json(App\Models\Method::all()->pluck('name', 'id'));

    let tempClients = {};
    let tempProjects = {};
    let tempClientIdCounter = -1;
    let tempProjectIdCounter = -1;

    const btnAddClient = document.getElementById('btn-add-client');
    const newClientForm = document.getElementById('new-client-form');
    const newClientName = document.getElementById('new-client-name');
    const btnSaveClient = document.getElementById('btn-save-client');
    const btnCancelClient = document.getElementById('btn-cancel-client');
    const clientSelect = document.querySelector('[name="client_id"]');

    btnAddClient.addEventListener('click', function() {
        newClientForm.classList.remove('d-none');
        newClientName.focus();
    });

    btnCancelClient.addEventListener('click', function() {
        newClientForm.classList.add('d-none');
        newClientName.value = '';
    });

    btnSaveClient.addEventListener('click', function() {
        const name = newClientName.value.trim();
        if (!name) {
            alert('Please enter a client name');
            return;
        }

        const tempId = tempClientIdCounter--;
        tempClients[tempId] = name;
        
        const option = new Option(name + ' (New)', tempId, true, true);
        clientSelect.add(option);
        
        newClientForm.classList.add('d-none');
        newClientName.value = '';
        
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show mt-2';
        alert.innerHTML = `New client "${name}" added. Will be saved on submission. <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        newClientForm.parentNode.insertBefore(alert, newClientForm.nextSibling);
        
        setTimeout(() => alert.remove(), 3000);
    });

    const btnAddProject = document.getElementById('btn-add-project');
    const newProjectForm = document.getElementById('new-project-form');
    const newProjectName = document.getElementById('new-project-name');
    const btnSaveProject = document.getElementById('btn-save-project');
    const btnCancelProject = document.getElementById('btn-cancel-project');
    const projectSelect = document.querySelector('[name="project_id"]');

    btnAddProject.addEventListener('click', function() {
        newProjectForm.classList.remove('d-none');
        newProjectName.focus();
    });

    btnCancelProject.addEventListener('click', function() {
        newProjectForm.classList.add('d-none');
        newProjectName.value = '';
    });

    btnSaveProject.addEventListener('click', function() {
        const name = newProjectName.value.trim();
        const clientId = clientSelect.value;
        
        if (!name) {
            alert('Please enter a project name');
            return;
        }
        
        if (!clientId) {
            alert('Please select a client first');
            return;
        }

        const tempId = tempProjectIdCounter--;
        tempProjects[tempId] = {
            name: name,
            client_id: clientId
        };

        const option = new Option(name + ' (New)', tempId, true, true);
        projectSelect.add(option);
        
        newProjectForm.classList.add('d-none');
        newProjectName.value = '';
        
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show mt-2';
        alert.innerHTML = `New project "${name}" added. Will be saved on submission. <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        newProjectForm.parentNode.insertBefore(alert, newProjectForm.nextSibling);
        
        setTimeout(() => alert.remove(), 3000);
    });

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
            const clientId = document.querySelector('#step-1 [name="client_id"]').value;
            const projectId = document.querySelector('#step-1 [name="project_id"]').value;
            
            formData.append('client_id', clientId);
            formData.append('project_id', projectId);
            
            if (clientId < 0) {
                formData.append('new_client_name', tempClients[clientId]);
            }
            
            if (projectId < 0) {
                formData.append('new_project_name', tempProjects[projectId].name);
            }
        } else if (step === 2) {
            formData.append('name', document.querySelector('#step-2 [name="name"]').value);
            formData.append('type', document.querySelector('#step-2 [name="type"]').value);
        } else if (step === 3) {
            formData.append('quantity', document.querySelector('#step-3 [name="quantity"]').value || '');
            formData.append('unit', document.querySelector('#step-3 [name="unit"]').value || '');
        } else if (step === 4) {
            document.querySelectorAll('#step-4 input[name="method_ids[]"]:checked').forEach(cb => {
                formData.append('method_ids[]', cb.value);
            });
        } else if (step === 5) {
            formData.append('confirm', '1');
        }
        return formData;
    }

    function fillReview() {
        const cid = document.querySelector('[name="client_id"]').value;
        const pid = document.querySelector('[name="project_id"]').value;
        let clientName = '-';
        let projectName = '-';
        
        if (cid < 0) {
            clientName = tempClients[cid] + ' (New)';
        } else if (cid && clients[cid]) {
            clientName = clients[cid];
        }
        
        if (pid < 0) {
            projectName = tempProjects[pid].name + ' (New)';
        } else if (pid && projects[pid]) {
            projectName = projects[pid];
        }
        
        document.getElementById('review-client').textContent = clientName;
        document.getElementById('review-project').textContent = projectName;
        document.getElementById('review-name').textContent = document.querySelector('[name="name"]').value || '-';
        document.getElementById('review-type').textContent = document.querySelector('[name="type"]').value || '-';
        document.getElementById('review-quantity').textContent = document.querySelector('#step-3 [name="quantity"]').value || '-';
        document.getElementById('review-unit').textContent = document.querySelector('#step-3 [name="unit"]').value || '-';
        const checked = Array.from(document.querySelectorAll('#step-4 input[name="method_ids[]"]:checked'))
            .map(cb => methods[cb.value] || cb.value).join(', ');
        document.getElementById('review-methods').textContent = checked || '-';
    }

    btnNext.addEventListener('click', function() {
        if (currentStep === totalSteps) {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            const clientId = document.querySelector('#step-1 [name="client_id"]').value;
            const projectId = document.querySelector('#step-1 [name="project_id"]').value;
            
            formData.append('client_id', clientId);
            formData.append('project_id', projectId);
            formData.append('name', document.querySelector('#step-2 [name="name"]').value);
            formData.append('type', document.querySelector('#step-2 [name="type"]').value);
            formData.append('quantity', document.querySelector('#step-3 [name="quantity"]').value || '');
            formData.append('unit', document.querySelector('#step-3 [name="unit"]').value || '');
            if (clientId < 0) {
                formData.append('new_client_name', tempClients[clientId]);
            }
            if (projectId < 0) {
                formData.append('new_project_name', tempProjects[projectId].name);
            }
            document.querySelectorAll('#step-4 input[name="method_ids[]"]:checked').forEach(cb => {
                formData.append('method_ids[]', cb.value);
            });

            btnNext.disabled = true;
            fetch('{{ route("samples.store-wizard") }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.redirect) {
                    window.location.href = data.redirect;
                } else if (data.errors) {
                    errorsEl.innerHTML = Object.values(data.errors).flat().join('<br>');
                    errorsEl.classList.remove('d-none');
                    btnNext.disabled = false;
                }
            })
            .catch(() => { errorsEl.innerHTML = 'Network error'; errorsEl.classList.remove('d-none'); btnNext.disabled = false; });
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
                if (currentStep === 4) fillReview();
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
