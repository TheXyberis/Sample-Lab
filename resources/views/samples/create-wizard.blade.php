@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <h3 class="text-primary">Sample Registration (Wizard)</h3>
    <div class="card mt-3">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item"><a class="nav-link active" data-step="1" href="#">Step 1: Client and project</a></li>
                <li class="nav-item"><a class="nav-link" data-step="2" href="#">Step 2: Name and type</a></li>
            </ul>
            <div id="step-1" class="wizard-step">
                <label class="form-label">Client</label>
                <select name="client_id" class="form-select mb-2">
                    <option value="">Select client</option>
                    @foreach(App\Models\Client::all() as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
                <label class="form-label">Project</label>
                <select name="project_id" class="form-select">
                    <option value="">Select project</option>
                    @foreach(App\Models\Project::all() as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div id="step-2" class="wizard-step d-none">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control mb-2">
                <label class="form-label">Type</label>
                <input type="text" name="type" class="form-control">
            </div>
            <div id="validation-errors" class="alert alert-danger d-none mt-3"></div>
            <div class="mt-3">
                <button type="button" class="btn btn-primary" id="btn-validate">Validate step</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btn-validate');
    const errorsEl = document.getElementById('validation-errors');
    let currentStep = 1;

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
        alert.innerHTML = `New client "${name}" added. <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
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
        alert.innerHTML = `New project "${name}" added. <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        newProjectForm.parentNode.insertBefore(alert, newProjectForm.nextSibling);
        
        setTimeout(() => alert.remove(), 3000);
    });

    document.querySelectorAll('[data-step]').forEach(el => {
        el.addEventListener('click', e => {
            e.preventDefault();
            currentStep = parseInt(el.dataset.step);
            document.querySelectorAll('.wizard-step').forEach(s => s.classList.add('d-none'));
            document.getElementById('step-' + currentStep).classList.remove('d-none');
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            el.classList.add('active');
            errorsEl.classList.add('d-none');
        });
    });
    
    btn.addEventListener('click', function() {
        const step1 = document.getElementById('step-1');
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        if (currentStep === 1) {
            const clientId = step1.querySelector('[name="client_id"]').value;
            const projectId = step1.querySelector('[name="project_id"]').value;
            
            formData.append('client_id', clientId);
            formData.append('project_id', projectId);
            
            if (clientId < 0) {
                formData.append('new_client_name', tempClients[clientId]);
            }
            
            if (projectId < 0) {
                formData.append('new_project_name', tempProjects[projectId].name);
            }
        } else {
            const step2 = document.getElementById('step-2');
            formData.append('name', step2.querySelector('[name="name"]').value);
            formData.append('type', step2.querySelector('[name="type"]').value);
        }

        fetch('{{ route("samples.validate-step", ":step") }}'.replace(':step', currentStep), {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            errorsEl.classList.add('d-none');
            if (data.valid) {
                if (currentStep === 1) {
                    currentStep = 2;
                    document.querySelectorAll('.wizard-step').forEach(s => s.classList.add('d-none'));
                    document.getElementById('step-2').classList.remove('d-none');
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    document.querySelector('[data-step="2"]').classList.add('active');
                } else {
                    alert(data.message || 'OK');
                }
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
});
</script>
@endsection
