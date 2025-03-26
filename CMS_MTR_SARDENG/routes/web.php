<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

// Redirect root URL to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// Blog routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/create', [BlogController::class, 'create'])->name('blog.create');
Route::post('/blog/store', [BlogController::class, 'store'])->name('blog.store');
Route::get('/blog/edit/{id}', [BlogController::class, 'edit'])->name('blog.edit');
Route::post('/blog/update/{id}', [BlogController::class, 'update'])->name('blog.update');
Route::post('/blog/delete/{id}', [BlogController::class, 'delete'])->name('blog.delete');

// Comment routes
Route::get('/blog/{post_id}/comments/create', [BlogController::class, 'createComment'])->name('comments.create');
Route::post('/blog/{post_id}/comments/store', [BlogController::class, 'storeComment'])->name('comments.store');
Route::get('/blog/{post_id}/comments/{id}/edit', [BlogController::class, 'editComment'])->name('comments.edit');
Route::post('/blog/{post_id}/comments/{id}/update', [BlogController::class, 'updateComment'])->name('comments.update');
Route::post('/blog/{post_id}/comments/{id}/delete', [BlogController::class, 'deleteComment'])->name('comments.delete');

// Category routes
Route::get('/categories/create', [BlogController::class, 'createCategory'])->name('categories.create');
Route::post('/categories/store', [BlogController::class, 'storeCategory'])->name('categories.store');

// Tag routes
Route::get('/tags/create', [BlogController::class, 'createTag'])->name('tags.create');
Route::post('/tags/store', [BlogController::class, 'storeTag'])->name('tags.store');

Route::get('/seed-categories-tags', [BlogController::class, 'seedCategoriesAndTags'])->name('seed.categories.tags');
Route::get('/add-category-tag', [BlogController::class, 'addCategoryAndTag'])->name('add.category.tag');