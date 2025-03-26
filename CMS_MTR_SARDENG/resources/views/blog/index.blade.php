@extends('layouts.app')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

@section('content')
    <h1>Blog Posts</h1>
    <div class="mb-3">
        <a href="{{ route('blog.create') }}" class="btn btn-outline-success">Create New Post</a>
        <a href="{{ route('categories.create') }}" class="btn btn-outline-primary">Add Category</a>
        <a href="{{ route('tags.create') }}" class="btn btn-outline-primary">Add Tag</a>
    </div>

    <div class="mb-3">
        <form action="{{ route('blog.index') }}" method="GET" class="d-flex">
            <select name="category" class="form-select me-2" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <select name="tag" class="form-select me-2" onchange="this.form.submit()">
                <option value="">All Tags</option>
                @foreach($tags as $tag)
                    <option value="{{ $tag->slug }}" {{ request('tag') == $tag->slug ? 'selected' : '' }}>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if($posts->count() > 0)
        <div class="row">
            @foreach($posts as $post)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3>{{ $post->title }}</h3>
                        </div>
                        <div class="card-body">
                            @if($post->photo)
                                <img src="{{ Storage::url($post->photo) }}" alt="{{ $post->title }}" class="img-fluid mb-3 d-block mx-auto" style="width: 250px; height: 250px; object-fit: cover;">
                            @endif
                            <p>{{ $post->body }}</p>
                           
                            <div class="mb-2">
                                <strong>Categories:</strong>
                                @foreach($post->categories as $category)
                                    <a href="{{ route('blog.index', ['category' => $category->slug]) }}" class="badge bg-primary me-1" style="text-decoration: none;">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                            <div class="mb-2">
                                <strong>Tags:</strong>
                                @foreach($post->tags as $tag)
                                    <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="badge bg-secondary me-1" style="text-decoration: none;">
                                        {{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                            <p><small>Created at: {{ $post->created_at }}</small></p>
                            <a href="{{ route('blog.edit', $post->id) }}" class="btn btn-outline-primary btn-sm">Edit Post</a>
                            <form action="{{ route('blog.delete', $post->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">Delete Post</button>
                            </form>
                            <a href="{{ route('comments.create', $post->id) }}" class="btn btn-outline-success btn-sm">Add Comment</a>

                            <h5 class="mt-3">Comments</h5>
                            @if(count($post->comments) > 0)
                                <ul class="list-group mb-3">
                                    @foreach($post->comments as $comment)
                                        <li class="list-group-item">
                                            <p>{{ $comment->body }}</p>
                                            <p><small>Posted at: {{ $comment->created_at }}</small></p>
                                            <a href="{{ route('comments.edit', [$post->id, $comment->id]) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                            <form action="{{ route('comments.delete', [$post->id, $comment->id]) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>No comments yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $posts->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    @else
        <p>No posts found.</p>
    @endif

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Include app.js -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Add JavaScript to handle fading out alerts -->
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Handle success alert
                const successAlert = document.getElementById('success-alert');
                if (successAlert) {
                    setTimeout(() => {
                        successAlert.classList.remove('show');
                        successAlert.classList.add('fade');
                    }, 5000);
                }

                // Handle error alert
                const errorAlert = document.getElementById('error-alert');
                if (errorAlert) {
                    setTimeout(() => {
                        errorAlert.classList.remove('show');
                        errorAlert.classList.add('fade');
                    }, 5000);
                }
            });
        </script>
    @endpush
    
@endsection