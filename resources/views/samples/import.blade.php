@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="text-primary mb-4">Import Samples CSV</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <label for="clientId" class="form-label">Client <span class="text-danger">*</span></label>
                <select id="clientId" class="form-select" required>
                    <option value="">Select client</option>
                    @foreach(\App\Models\Client::all() as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="projectId" class="form-label">Project</label>
                <select id="projectId" class="form-select">
                    <option value="">Select project (optional)</option>
                    @foreach(\App\Models\Project::all() as $project)
                        <option value="{{ $project->id }}" data-client="{{ $project->client_id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="csvFile" class="form-label">CSV File <span class="text-danger">*</span></label>
                <input type="file" id="csvFile" class="form-control" accept=".csv,.txt" required>
            </div>
            <button id="uploadBtn" class="btn btn-primary">Upload & Preview</button>
            <a href="{{ route('samples.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>

            <div id="preview" class="mt-4"></div>
        </div>
    </div>
</div>
@endsection 
 
@section('scripts') 
<script> 
document.addEventListener("DOMContentLoaded", function () { 
    let importPath = null; 
    const uploadBtn = document.getElementById('uploadBtn'); 
    if (!uploadBtn) return; 
 
    uploadBtn.addEventListener('click', async () => { 
        const file = document.getElementById('csvFile').files[0]; 
        if (!file) { alert("Please select a file"); return; } 
 
        const form = new FormData(); 
        form.append('file', file); 
 
        try { 
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('{{ route("samples.import.preview") }}', { 
                method: 'POST', 
                headers: { 
                    'X-CSRF-TOKEN': token || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }, 
                body: form, 
                credentials: 'same-origin' 
            }); 
 
            if (!res.ok){ 
                const text = await res.text(); 
                alert("Error: " + res.status + "\n" + text); 
                return; 
            } 
 
            const data = await res.json(); 
            importPath = data.path; 
            renderPreview(data.preview); 
 
        } catch (e) { 
            console.error(e); 
            alert("CSV upload failed"); 
        } 
    }); 
 
    function renderPreview(rows){
        let html = '<div class="table-responsive"><table class="table table-bordered table-sm">';
        rows.forEach((row, rowIndex) => {
            html += '<tr>';
            row.forEach((col, i) => {
                if(rowIndex === 0){
                    html += `<th>
                        ${escapeHtml(String(col))}<br>
                        <select class="form-select form-select-sm mapping" data-col="col${i}">
                            <option value="">skip</option>
                            <option value="sample_code">sample_code</option>
                            <option value="name">name</option>
                            <option value="type">type</option>
                            <option value="quantity">quantity</option>
                            <option value="unit">unit</option>
                        </select>
                    </th>`;
                } else {
                    html += `<td>${escapeHtml(String(col))}</td>`;
                }
            });
            html += '</tr>';
        });
        html += '</table></div>';
        html += '<button id="confirmBtn" class="btn btn-success mt-2">Import Samples</button>';
        document.getElementById('preview').innerHTML = html;
        document.getElementById('confirmBtn').addEventListener('click', confirmImport);
    }
    function escapeHtml(s){ return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); } 
 
    async function confirmImport(){
        let mapping = {};
        document.querySelectorAll('.mapping').forEach(el => {
            if(el.value) mapping[el.value] = el.dataset.col;
        });

        const clientId = document.getElementById('clientId').value;
        const projectId = document.getElementById('projectId').value;

        if(!clientId){ alert('Please select a client'); return; }
        if(!importPath){ alert('Please upload a file first'); return; }

        const payload = { path: importPath, mapping: mapping, client_id: parseInt(clientId) };
        if(projectId) payload.project_id = parseInt(projectId);

        try {
            const res = await fetch('{{ route("samples.import.confirm") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload),
                credentials: 'same-origin'
            });

            const data = await res.json();
            if(!res.ok){
                alert("Import failed: " + (data.error || data.message || res.status));
                return;
            }

            alert('Imported ' + data.created + ' samples successfully');
            window.location.href = '{{ route("samples.index") }}';
        } catch(e) {
            console.error(e);
            alert("Import request failed");
        }
    } 
}); 
</script> 
@endsection