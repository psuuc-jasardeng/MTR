@extends('layouts.app')

@section('content')
    <h1>Edit Comment</h1>
    <form method="POST" action="{{ route('comments.update', [$post_id, $comment->id]) }}">
        @csrf
        <div class="mb-3">
            <label for="body" class="form-label">Comment</label>
            <textarea name="body" id="body" class="form-control" required>{{ $comment->body }}</textarea>
        </div>
        <button type="submit" class="btn btn-outline-primary">Update Comment</button>
    </form>
@endsection