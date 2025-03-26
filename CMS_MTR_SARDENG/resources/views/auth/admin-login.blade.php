@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">Admin Login</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login.submit') }}">
                        @csrf
                        <input type="hidden" name="is_admin" value="1">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-danger w-100">Login</button>
                        </div>
                    </form>
                    <p class="text-center">
                        <a href="{{ route('login') }}" class="text-success">Login as Client</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection