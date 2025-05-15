<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GoogleDriveController;


Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth:web'])->group(function () {


    
});

Route::middleware('auth:web', 'role:user')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/upload', [DocumentController::class, 'showUploadForm'])->name('upload.form');
    Route::post('/documents/upload-check', [DocumentController::class, 'upload'])->name('documents.uploadCheck');
    Route::post('/documents/upload-reference', [DocumentController::class, 'uploadReference'])->name('documents.uploadReference');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth:web', 'role:admin'])->group(function () {

    Route::get('/admin/users', [UsersController::class, 'usersPage'])->name('admin.users');
    Route::put('/admin/users/{user}', [UsersController::class, 'update'])->name('users.update');
    Route::get('/admin/profile', [ProfileController::class, 'editAdmin'])->name('admin.profile.edit');
    Route::patch('/admin/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::delete('/admin/profile', [ProfileController::class, 'destroy'])->name('admin.profile.destroy');
    Route::get('/download-files', [GoogleDriveController::class, 'downloadAndSaveToDatabase'])->name('download.files');
    Route::get('/admin/documents', [DocumentController::class, 'documentPage'])->name('admin.documents');
    Route::get('/documents/{id}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/admin/{page}', [DocumentController::class, 'adminPage'])->name('admin.page');
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy')->middleware('auth');

});
    

// Route::get('/download-reference-docs', [GoogleDriveController::class, 'downloadAndSaveToDatabase']);
require __DIR__.'/auth.php';
