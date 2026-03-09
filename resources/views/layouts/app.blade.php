<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <title>SampleLab</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <style> 
        body { background-color: #f8f9fa; } 
        .navbar-brand { font-weight: 600; color: #0d6efd; } 
        .card { border-radius: 0.5rem; } 
        .badge { text-transform: uppercase; font-size: 0.85em; } 
        .table-hover tbody tr:hover { background-color: #e9f2ff; } 
        .nav-link.active { font-weight: 500; color: #0d6efd !important; } 
    </style> 
</head> 
<body> 
 
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4"> 
    <div class="container"> 
        <a class="navbar-brand" href="{{ route('dashboard') }}">SampleLab</a> 
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"> 
            <span class="navbar-toggler-icon"></span> 
        </button> 
        <div class="collapse navbar-collapse" id="navbarNav"> 
            <ul class="navbar-nav me-auto mb-2 mb-lg-0"> 
                @auth 
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a></li> 
                     
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('samples.index') ? 'active' : '' }}" href="{{ route('samples.index') }}">Samples</a></li> 
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('samples.import') ? 'active' : '' }}" href="{{ route('samples.import') }}">Import CSV</a></li> 
                     
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('methods.*') ? 'active' : '' }}" href="{{ route('methods.index') }}">Methods</a></li> 
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('measurements.*') ? 'active' : '' }}" href="{{ route('measurements.index') }}">Measurements</a></li> 
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('qc.queue') ? 'active' : '' }}" href="{{ route('qc.queue') }}">QC Queue</a></li> 
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">Reports</a></li> 
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Users</a></li> 
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}" href="{{ route('audit.index') }}">Audit</a></li> 
                @endauth 
            </ul> 
 
            <ul class="navbar-nav mb-2 mb-lg-0 d-flex align-items-center"> 
                @auth 
                    <li class="nav-item me-2"> 
                        <span class="nav-link text-muted">{{ Auth::user()->name }} ({{ Auth::user()->role }})</span> 
                    </li> 
                    <li class="nav-item"> 
                        <form method="POST" action="{{ route('logout') }}" class="d-flex align-items-center m-0 p-0"> 
                            @csrf 
                            <button class="btn btn-sm btn-outline-danger" type="submit">Logout</button> 
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
    @if(session('success')) 
        <div class="alert alert-success alert-dismissible fade show" role="alert"> 
            {{ session('success') }} 
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button> 
        </div> 
    @endif 
    @if(session('error')) 
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
[3/9/2026 12:59 PM] вика флекс: {{ session('error') }} 
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button> 
        </div> 
    @endif 
 
    @yield('content') 
</div> 
 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> 
@yield('scripts') 
</body> 
</html>