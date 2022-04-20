@extends('main')

@section('content')
<div class="container">
    <div class="starter-template">
        <div class="mb-5 text-center">
            <img class="mb-3" src="/php.png" alt="" width="72" height="72">
            <h2>Welcome</h2>
            <p class="lead">Sign in with your credentials</p>
        </div>

        <form action="/signin" method="POST">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <p class="text-end"><a href="/signup" class="text-light">Become our customer</a></p>
            <input type="submit" class="btn btn-dark" value="Login">
            <button type="reset" class="btn btn-secondary">Cancel</button>
        </form>
        @if (session('status'))
            <div class="alert alert-{{ session('status')['alert'] }} alert-dismissible fade show mt-3" role="alert">
                {{ session('status')['message'] }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
</div>
@endsection