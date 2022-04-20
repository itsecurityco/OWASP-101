<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <title>{{ Config::get('app.name') }}</title>
    <style>
        .bg-dark {
            --bs-bg-opacity: 1;
            background: #333333 url(/bg-texture-00.svg) !important; /* dark: #212529 */
        }

        .navbar {
            background-color: #8892BF;
            border-bottom: 5px solid #4F5B93 !important;
        }

        .navbar-brand {
            color: black !important;
        }

        .btn-dark {
            background-color: #4F5B93 !important;
            border-color: #4F5B93 !important;
            color: white;
        }

        .btn-dark:hover {
            background-color: #505c90 !important;
            border-color: #505c90 !important;
            color: white;
        }

        .starter-template {
            padding: 3rem 1.5rem;
        }

        .container {
            max-width: 960px;
        }
    </style>
</head>
<body class="bg-dark text-white d-flex flex-column min-vh-100">
    @auth
        <nav class="navbar navbar-expand-md navbar-dark text-white">
            <div class="container">
                <a class="navbar-brand" href="/"><strong>{{ Config::get('app.name') }}</strong></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-0 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::path() === 'received' ? 'active' : '' }}" href="{{ route('received') }}">
                                Transfer Received
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::path() === 'sent' ? 'active' : '' }}" href="{{ route('sent') }}">
                                Transfer Sent
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::path() === 'transfer' ? 'active' : '' }}" href="{{ route('transfer') }}">
                                Transfer
                            </a>
                        </li>
                    </ul>
                    <div class="d-flex">
                        <a class='nav-link text-white'>Welcome {{ Auth::user()->fullname }}</a>
                        <a href="/logout">
                            <button class='btn btn-outline-light' type='button'>
                                Logout
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    @endauth

    @yield('content')
    
    <footer class="mt-auto py-3 border-top">
        <ul class="nav justify-content-center list-unstyled d-flex">
            <li>
                <span class="text-muted">Developed by <a href="https://www.linkedin.com/in/srescobar/" class="text-white">Juan Escobar</a></span>
            </li>
            <li class="ms-2"><a class="text-muted" href="https://github.com/itsecurityco">
                <i class="bi bi-github"></i>
            </li>
            <li class="ms-2"><a class="text-muted" href="https://twitter.com/itsecurityco">
                    <i class="bi bi-twitter"></i>
            </li>
        </ul>
    </footer>

    <!-- Optional JavaScript -->
    @yield('javascript')
    <!-- Popper.js, Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
        integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13"
        crossorigin="anonymous"></script>
</body>
</html>