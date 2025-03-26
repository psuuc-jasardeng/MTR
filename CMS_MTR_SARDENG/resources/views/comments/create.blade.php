@extends('layouts.app')

@section('content')
    <h1>Add Comment to Post</h1>
    <form method="POST" action="{{ route('comments.store', $post_id) }}">
        @csrf
        <div class="mb-3">
            <label for="body" class="form-label">Comment</label>
            <textarea name="body" id="body" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-outline-primary">Add Comment</button>
    </form>
@endsection