<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SampleLab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">SampleLab</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('samples.index') }}">Samples</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('samples.import') }}">Import CSV</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('methods.index') }}">Methods</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('measurements.index') }}">Measurements</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('qc.queue') }}">QC Queue</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('reports.index') }}">Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('audit.index') }}">Audit</a></li>
                @endauth
            </ul>
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item"><span class="nav-link">{{ Auth::user()->name }} ({{ Auth::user()->role }})</span></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="btn btn-sm btn-outline-danger">Logout</button>
                        </form>
                    </li>
                @endauth
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>