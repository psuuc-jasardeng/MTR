@extends('layouts.app')

@section('content')
    <h1>Create New Tag</h1>
    <form method="POST" action="{{ route('tags.store') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Tag Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-outline-primary">Create Tag</button>
        <a href="{{ route('blog.index') }}" class="btn btn-outline-danger">Cancel</a>
    </form>
@endsection