@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Import Samples CSV</h3>

    <div class="mb-3">
        <label for="clientId" class="form-label">Select Client</label>
        <select id="clientId" class="form-control">
            @foreach(\App\Models\Client::all() as $client)
                <option value="{{ $client->id }}">{{ $client->name }}</option>
            @endforeach
        </select>
    </div>

    <input type="file" id="csvFile" class="form-control mb-2">
    <button id="uploadBtn" class="btn btn-primary mb-3">Upload</button>
    
    <div id="preview"></div>
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
            const res = await fetch('{{ route("samples.import.preview") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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
        let html = '<table class="table table-bordered">';
        rows.forEach((row, rowIndex) => {
            html += '<tr>';
            row.forEach((col, i) => {
                if(rowIndex === 0){
                    html += `
                    <th>
                        ${col}<br>
                        <select class="mapping" data-col="col${i}">
                            <option value="">skip</option>
                            <option value="sample_code">sample_code</option>
                            <option value="name">name</option>
                            <option value="type">type</option>
                            <option value="quantity">quantity</option>
                            <option value="unit">unit</option>
                        </select>
                    </th>`;
                } else {
                    html += `<td>${col}</td>`;
                }
            });
            html += '</tr>';
        });
        html += '</table>';
        html += '<button id="confirmBtn" class="btn btn-success mt-2">Import</button>';
        document.getElementById('preview').innerHTML = html;

        document.getElementById('confirmBtn').addEventListener('click', confirmImport);
    }

    async function confirmImport(){
        let mapping = {};
        document.querySelectorAll('.mapping').forEach(el => {
            if(el.value) mapping[el.value] = el.dataset.col;
        });

        const clientId = document.getElementById('clientId').value;

        try {
            const res = await fetch('{{ route("samples.import.confirm") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    path: importPath, 
                    mapping: mapping,
                    client_id: clientId 
                }),
                credentials: 'same-origin'
            });

            if(!res.ok){
                const text = await res.text();
                alert("Import failed: " + res.status + "\n" + text);
                return;
            }

            const data = await res.json();
            alert('Imported ' + data.created + ' samples successfully');
            location.reload();

        } catch(e) {
            console.error(e);
            alert("Import request failed");
        }
    }
});
</script>
@endsection