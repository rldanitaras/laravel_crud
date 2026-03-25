<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::prefix('posts')->name('posts.')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('index');           // List posts
        Route::post('/', [PostController::class, 'store'])->name('store');          // Store new post
        Route::put('/{post}', [PostController::class, 'update'])->name('update');   // Update post
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy'); // Delete post
    });
});

Route::get('/hello', function () {
    return view('hello.test');
});


require __DIR__.'/auth.php';
