<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

Route::post('/documents/upload', [DocumentController::class, 'upload']);
Route::get('/documents', [DocumentController::class, 'list']);

Route::post('/reference-documents/upload', [DocumentController::class, 'uploadReference']);
Route::get('/reference-documents', [DocumentController::class, 'listReferences']);
Route::post('/check-plagiarism', [DocumentController::class, 'checkPlagiarism'])->name('check.plagiarism');

