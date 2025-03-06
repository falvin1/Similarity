<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\GoogleDriveController;

Route::get('/upload', [DocumentController::class, 'showUploadForm'])->name('upload.form');
Route::post('/documents/upload-check', [DocumentController::class, 'upload'])->name('documents.uploadCheck');
Route::post('/documents/upload-reference', [DocumentController::class, 'uploadReference'])->name('documents.uploadReference');




Route::get('/download-reference-docs', [GoogleDriveController::class, 'downloadAndSaveToDatabase']);
