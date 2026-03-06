@extends('layouts.app')

@section('content')

<h1>Samples</h1>

<div class="mb-3">

    <input id="search" placeholder="Search">

    <select id="status">
        <option value="">All statuses</option>
        <option value="new">New</option>
        <option value="in_progress">In progress</option>
        <option value="done">Done</option>
    </select>

    <select id="client">
        <option value="">All clients</option>
    </select>

    <button onclick="loadSamples()">Filter</button>

</div>

<table class="table">
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Status</th>
<th>Client</th>
</tr>
</thead>

<tbody id="samplesTable">
<tr>
<td colspan="4">Loading...</td>
</tr>
</tbody>

</table>

<div id="pagination"></div>

@endsection


<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>

let currentPage = 1;

function loadSamples(page = 1) {

    currentPage = page;

    let status = document.getElementById('status').value;
    let client = document.getElementById('client').value;
    let q = document.getElementById('search').value;

    axios.get('/samples', {
        params: {
            page: page,
            status: status,
            client: client,
            q: q
        }
    })
    .then(response => {

        let samples = response.data.data;
        let tbody = document.getElementById('samplesTable');

        tbody.innerHTML = '';

        if(samples.length === 0){
            tbody.innerHTML = `<tr><td colspan="4">Brak danych</td></tr>`;
            return;
        }

        samples.forEach(sample => {

            tbody.innerHTML += `
                <tr>
                    <td>${sample.id}</td>
                    <td>${sample.name}</td>
                    <td>${sample.status}</td>
                    <td>${sample.client_name}</td>
                </tr>
            `;
        });

        renderPagination(response.data);

    });

}

function renderPagination(data){

    let container = document.getElementById('pagination');

    container.innerHTML = '';

    for(let i = 1; i <= data.last_page; i++){

        container.innerHTML += `
            <button onclick="loadSamples(${i})">${i}</button>
        `;

    }

}

loadSamples();

</script>