<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel App')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite('resources/css/app.css')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">MyApp</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <form id="scanForm" class="d-flex me-3">
                    <input 
                        type="text" 
                        id="scanCode"
                        class="form-control"
                        placeholder="Scan code">
                </form>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/about') }}">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/contact') }}">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('scanForm').addEventListener('submit', function(e){

        e.preventDefault();

        let code = document.getElementById('scanCode').value;

        axios.get('/samples', {
            params: { q: code }
        })
        .then(response => {

            let samples = response.data.data;

            if(samples.length > 0){

                let id = samples[0].id;

                window.location.href = '/samples/' + id;

            }else{
                alert("Sample not found");
            }

        });

    });
    </script>
    @vite('resources/js/app.js')
</body>
</html>