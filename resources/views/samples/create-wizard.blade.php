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
            formData.append('client_id', step1.querySelector('[name="client_id"]').value);
            formData.append('project_id', step1.querySelector('[name="project_id"]').value);
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
                alert(data.message || 'OK');
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
