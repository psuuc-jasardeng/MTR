<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    private function checkAuth(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        return null;
    }

    public function index(Request $request)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        $categorySlug = $request->query('category');
        $tagSlug = $request->query('tag');

        $query = DB::table('posts');

        if ($categorySlug) {
            $category = DB::table('categories')->where('slug', $categorySlug)->first();
            if ($category) {
                $postIds = DB::table('category_post')
                    ->where('category_id', $category->id)
                    ->pluck('post_id')
                    ->toArray();
                $query->whereIn('id', $postIds);
            }
        }

        if ($tagSlug) {
            $tag = DB::table('tags')->where('slug', $tagSlug)->first();
            if ($tag) {
                $postIds = DB::table('post_tag')
                    ->where('tag_id', $tag->id)
                    ->pluck('post_id')
                    ->toArray();
                $query->whereIn('id', $postIds);
            }
        }

        $posts = $query->paginate(3);

        foreach ($posts as $post) {
            $post->comments = DB::table('comments')
                ->where('post_id', $post->id)
                ->get()
                ->toArray();

            $post->categories = DB::table('categories')
                ->join('category_post', 'categories.id', '=', 'category_post.category_id')
                ->where('category_post.post_id', $post->id)
                ->select('categories.*')
                ->get()
                ->toArray();

            $post->tags = DB::table('tags')
                ->join('post_tag', 'tags.id', '=', 'post_tag.tag_id')
                ->where('post_tag.post_id', $post->id)
                ->select('tags.*')
                ->get()
                ->toArray();
        }

        $categories = DB::table('categories')->get()->toArray();
        $tags = DB::table('tags')->get()->toArray();

        return view('blog.index', compact('posts', 'categories', 'tags'));
    }

    public function create(Request $request)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        $categories = DB::table('categories')->get()->toArray();
        $tags = DB::table('tags')->get()->toArray();

        return view('blog.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categories' => 'required|array',
            'tags' => 'required|array|size:1',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        $postId = DB::table('posts')->insertGetId([
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'photo' => $photoPath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($request->input('categories') as $categoryId) {
            DB::table('category_post')->insert([
                'post_id' => $postId,
                'category_id' => $categoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $tagId = $request->input('tags')[0];
        DB::table('post_tag')->insert([
            'post_id' => $postId,
            'tag_id' => $tagId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Ensure the session message is set only once
        return redirect()->route('blog.index')->with('success', 'Post created successfully!');
    }

    public function edit(Request $request, $id)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        $post = DB::table('posts')->where('id', $id)->first();
        if (!$post) {
            return redirect()->route('blog.index')->with('error', 'Post not found.');
        }

        $categories = DB::table('categories')->get()->toArray();
        $tags = DB::table('tags')->get()->toArray();

        $postCategories = DB::table('category_post')
            ->where('post_id', $id)
            ->pluck('category_id')
            ->toArray();

        $postTags = DB::table('post_tag')
            ->where('post_id', $id)
            ->pluck('tag_id')
            ->toArray();

        return view('blog.edit', compact('post', 'categories', 'tags', 'postCategories', 'postTags'));
    }

    public function update(Request $request, $id)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        $postExists = DB::table('posts')->where('id', $id)->exists();
        if (!$postExists) {
            return redirect()->route('blog.index')->with('error', 'Post not found.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categories' => 'required|array',
            'tags' => 'required|array|size:1',
        ]);

        $post = DB::table('posts')->where('id', $id)->first();
        $photoPath = $post->photo;

        if ($request->hasFile('photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        DB::table('posts')
            ->where('id', $id)
            ->update([
                'title' => $request->input('title'),
                'body' => $request->input('body'),
                'photo' => $photoPath,
                'updated_at' => now(),
            ]);

        DB::table('category_post')->where('post_id', $id)->delete();
        foreach ($request->input('categories') as $categoryId) {
            DB::table('category_post')->insert([
                'post_id' => $id,
                'category_id' => $categoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('post_tag')->where('post_id', $id)->delete();
        $tagId = $request->input('tags')[0];
        DB::table('post_tag')->insert([
            'post_id' => $id,
            'tag_id' => $tagId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Ensure the session message is set only once
        return redirect()->route('blog.index')->with('success', 'Post updated successfully!');
    }

    public function delete(Request $request, $id)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        $post = DB::table('posts')->where('id', $id)->first();
        if (!$post) {
            return redirect()->route('blog.index')->with('error', 'Post not found.');
        }

        if ($post->photo) {
            Storage::disk('public')->delete($post->photo);
        }

        DB::table('posts')->where('id', $id)->delete();
        return redirect()->route('blog.index')->with('success', 'Post deleted successfully!');
    }

    public function createComment(Request $request, $post_id)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        $post = DB::table('posts')->where('id', $post_id)->first();
        if (!$post) {
            return redirect()->route('blog.index')->with('error', 'Post not found.');
        }

        return view('comments.create', compact('post_id'));
    }

    public function storeComment(Request $request, $post_id)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        $postExists = DB::table('posts')->where('id', $post_id)->exists();
        if (!$postExists) {
            return redirect()->route('blog.index')->with('error', 'Post not found.');
        }

        DB::table('comments')->insert([
            'post_id' => $post_id,
            'body' => $request->input('body'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('blog.index')->with('success', 'Comment added successfully!');
    }

    public function editComment(Request $request, $post_id, $id)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        $post = DB::table('posts')->where('id', $post_id)->first();
        if (!$post) {
            return redirect()->route('blog.index')->with('error', 'Post not found.');
        }

        $comment = DB::table('comments')
            ->where('id', $id)
            ->where('post_id', $post_id)
            ->first();

        if (!$comment) {
            return redirect()->route('blog.index')->with('error', 'Comment not found.');
        }

        return view('comments.edit', compact('post_id', 'comment'));
    }

    public function updateComment(Request $request, $post_id, $id)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        $postExists = DB::table('posts')->where('id', $post_id)->exists();
        if (!$postExists) {
            return redirect()->route('blog.index')->with('error', 'Post not found.');
        }

        $commentExists = DB::table('comments')
            ->where('id', $id)
            ->where('post_id', $post_id)
            ->exists();
        if (!$commentExists) {
            return redirect()->route('blog.index')->with('error', 'Comment not found.');
        }

        DB::table('comments')
            ->where('id', $id)
            ->where('post_id', $post_id)
            ->update([
                'body' => $request->input('body'),
                'updated_at' => now(),
            ]);

        return redirect()->route('blog.index')->with('success', 'Comment updated successfully!');
    }

    public function deleteComment(Request $request, $post_id, $id)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        $postExists = DB::table('posts')->where('id', $post_id)->exists();
        if (!$postExists) {
            return redirect()->route('blog.index')->with('error', 'Post not found.');
        }

        DB::table('comments')
            ->where('id', $id)
            ->where('post_id', $post_id)
            ->delete();

        return redirect()->route('blog.index')->with('success', 'Comment deleted successfully!');
    }

    public function createCategory(Request $request)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        return view('categories.create');
    }

    public function storeCategory(Request $request)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        $name = $request->input('name');
        $slug = Str::slug($name);

        $slugExists = DB::table('categories')->where('slug', $slug)->exists();
        if ($slugExists) {
            return redirect()->back()->with('error', 'A category with this name already exists (slug conflict).');
        }

        DB::table('categories')->insert([
            'name' => $name,
            'slug' => $slug,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('blog.index')->with('success', 'Category created successfully!');
    }

    public function createTag(Request $request)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        return view('tags.create');
    }

    public function storeTag(Request $request)
    {
        $authCheck = $this->checkAuth($request);
        if ($authCheck) {
            return $authCheck;
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:tags',
        ]);

        $name = $request->input('name');
        $slug = Str::slug($name);

        $slugExists = DB::table('tags')->where('slug', $slug)->exists();
        if ($slugExists) {
            return redirect()->back()->with('error', 'A tag with this name already exists (slug conflict).');
        }

        DB::table('tags')->insert([
            'name' => $name,
            'slug' => $slug,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('blog.index')->with('success', 'Tag created successfully!');
    }
}