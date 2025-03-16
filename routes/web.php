<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GoogleDriveController;


Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth:web'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/admin/profile', [ProfileController::class, 'editAdmin'])->name('admin.profile.edit');
    Route::patch('/admin/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::delete('/admin/profile', [ProfileController::class, 'destroy'])->name('admin.profile.destroy');
});

Route::middleware('auth:web', 'role:user')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/upload', [DocumentController::class, 'showUploadForm'])->name('upload.form');
    Route::post('/documents/upload-check', [DocumentController::class, 'upload'])->name('documents.uploadCheck');
    Route::post('/documents/upload-reference', [DocumentController::class, 'uploadReference'])->name('documents.uploadReference');
});


Route::middleware(['auth:web', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    // Route::get('/download-reference-docs', [GoogleDriveController::class, 'downloadAndSaveToDatabase']);
});

require __DIR__.'/auth.php';
