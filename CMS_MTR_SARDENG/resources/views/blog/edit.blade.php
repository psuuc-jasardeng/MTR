@extends('layouts.app')

@section('content')
    <h1>Edit Post</h1>
    <form method="POST" action="{{ route('blog.update', $post->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $post->title }}" required>
        </div>
        <div class="mb-3">
            <label for="body" class="form-label">Body</label>
            <textarea name="body" id="body" class="form-control" required>{{ $post->body }}</textarea>
        </div>
        <div class="mb-3">
            <label for="photo" class="form-label">Upload Photo</label>
            <input type="file" name="photo" id="photo" class="form-control">
            @if($post->photo)
                <div class="mt-2">
                    <img src="{{ Storage::url($post->photo) }}" alt="Post Photo" style="max-width: 200px;">
                </div>
            @endif
        </div>
        <div class="mb-3">
            <label for="categories" class="form-label">Categories</label>
            <select name="categories[]" id="categories" class="form-control" multiple required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ in_array($category->id, $postCategories) ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple categories.</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Tags</label>
            <div class="d-flex flex-wrap">
                @foreach($tags as $tag)
                    <div class="form-check me-3 mb-2">
                        <input type="radio" name="tags[]" id="tag-{{ $tag->id }}" value="{{ $tag->id }}" class="form-check-input custom-radio" {{ in_array($tag->id, $postTags) ? 'checked' : '' }} required>
                        <label for="tag-{{ $tag->id }}" class="form-check-label">{{ $tag->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Post</button>
    </form>

    <!-- Custom CSS for square radio buttons -->
    <style>
        .custom-radio {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #007bff;
            border-radius: 4px;
            outline: none;
            cursor: pointer;
            vertical-align: middle;
            margin-right: 5px;
        }

        .custom-radio:checked {
            background-color: #007bff;
            border-color: #007bff;
            position: relative;
        }

        .custom-radio:checked::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 10px;
            height: 10px;
            background-color: white;
            transform: translate(-50%, -50%);
            border-radius: 2px;
        }

        .form-check-label {
            cursor: pointer;
        }
    </style>
@endsection