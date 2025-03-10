<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;

// ✅ Halaman utama
Route::get('/', function () {
    return view('welcome');
});
Route::get('/upload', [DocumentController::class, 'showUploadForm'])->name('upload.form');
Route::post('/documents/upload-check', [DocumentController::class, 'upload'])->name('documents.uploadCheck');
Route::post('/documents/upload-reference', [DocumentController::class, 'uploadReference'])->name('documents.uploadReference');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// ✅ Halaman Upload untuk User (Hanya User Biasa yang Bisa Akses)
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/upload', [DocumentController::class, 'showUploadForm'])->name('upload.form');
    Route::post('/documents/upload-check', [DocumentController::class, 'upload'])->name('documents.uploadCheck');
    Route::post('/documents/upload-reference', [DocumentController::class, 'uploadReference'])->name('documents.uploadReference');
});

// // ✅ Halaman Admin (Hanya Admin yang Bisa Akses)
// Route::middleware(['auth', 'role:admin'])->group(function () {
//     Route::get('/admin/dashboard', function () {
//         return view('admin.dashboard');
//     })->name('admin.dashboard');
//     // Route::get('/download-reference-docs', [GoogleDriveController::class, 'downloadAndSaveToDatabase']);
// });

// // ✅ Pastikan Route Auth Tetap Aktif (Dari Laravel Breeze)
require __DIR__.'/auth.php';
