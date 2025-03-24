<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Models\DocumentHistory;
use App\Models\ReferenceDocument;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Smalot\PdfParser\Parser; // Import PDF Parser

class DocumentController extends Controller
{


    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048',
        ]);
    
        Log::info('File received:', ['filename' => $request->file('file')->getClientOriginalName()]);
        $file = $request->file('file');
        $filePath = $file->store('documents', 'public');
    
        try {
            // Kirim file ke Flask untuk preprocessing (multipart/form-data)
            $response = Http::withoutVerifying()->attach(
                'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
            )->post('http://127.0.0.1:5000/preprocess');
    
            if ($response->failed()) {
                return redirect()->back()->with('error', 'Gagal memproses file dengan Flask');
            }
    
            $responseData = $response->json();
    
            // Simpan dokumen ke database
            $document = Document::create([
                'user_id' => Auth::id(), 
                'title' => $file->getClientOriginalName(),
                'content' => $responseData['original_text'] ?? '',
                'preprocessed_content' => $responseData['preprocessed_text'] ?? '',
                'file_path' => $filePath,
            ]);
    
            // Ambil semua dokumen referensi
            $references = ReferenceDocument::whereNotNull('preprocessed_content')->get(['id', 'title', 'preprocessed_content']);
    
            if ($references->isEmpty()) {
                return redirect()->back()->with('message', 'File berhasil diunggah, tetapi tidak ada referensi untuk pemeriksaan plagiarisme.');
            }
    
            $referencesData = $references->map(function ($ref) {
                return [
                    'id' => $ref->id,
                    'title' => $ref->title,
                    'content' => $ref->preprocessed_content,
                ];
            })->toArray();
    
            if (empty($referencesData)) {
                return redirect()->back()->with('error', 'References cannot be empty');
            }
    
            // Tambahkan Log untuk Debugging

    
            // Kirim ke Flask untuk pengecekan plagiarisme (multipart/form-data untuk file, JSON untuk references)
            $formattedReferences = ['references' => $referencesData];
            $jsonString = json_encode($formattedReferences);

    
            $plagiarismResponse = Http::withoutVerifying()
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post('http://127.0.0.1:5000/check-plagiarism', [
                    'references' => $jsonString,
                ]);

    
            if ($plagiarismResponse->failed()) {
                return redirect()->back()->with('error', 'Gagal memeriksa plagiarisme dengan Flask');
            }
    
            $plagiarismResult = $plagiarismResponse->json();
            $comparisons = $plagiarismResult['comparisons'] ?? [];
            
            
            // Cari similarity tertinggi
            $highest = collect($comparisons)->sortByDesc('similarity')->first();
            if ($highest) {
                $document->similarity_percentage = $highest['similarity'];

                $referenceDoc = ReferenceDocument::where('id', $highest['reference_id'])->first();
                $googleDriveLink = $referenceDoc ? $referenceDoc->google_drive_link : null;
                
                $document->save();
                $reference_id = $highest['reference_id'] ?? null;
                DocumentHistory::create([
                    'user_id' => Auth::id(),
                    'document_id' => $document->id,
                    'similarity_percentage' => $highest['similarity'] ?? null,
                    'reference_document_id' => $reference_id,
                ]);
            }

            $history = DocumentHistory::with('referenceDocument')->where('document_id', $document->id)->first();
            $googleDriveLink_history = $history && $history->referenceDocument ? $history->referenceDocument->google_drive_link : null;

            // Redirect ke view dengan hasil similarity
            return redirect()->back()->with('result', [
                'highest_similarity' => $highest,
                'comparisons' => $comparisons,
                'google_drive_link' => $googleDriveLink ?? null,
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }    public function list()
    {
        $documents = Document::all();
    
        return response()->json([
            'message' => 'List of uploaded documents',
            'data' => $documents
        ], 200);
    }

    public function uploadReference(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'file' => 'required|mimes:pdf',
        ]);
    
        $file = $request->file('file');
        $filePath = $file->store('reference_documents', 'public');
    
        try {
            // Kirim file ke Flask API untuk preprocessing
            $response = Http::withoutVerifying()->attach(
                'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
            )->post('http://127.0.0.1:5000/preprocess');
    
            if ($response->failed()) {
                throw new \Exception('Gagal memproses file dengan Flask');
            }
    
            $responseData = $response->json();
    
            // Simpan ke database
            $reference = ReferenceDocument::create([
                'title' => $request->title,
                'file_path' => $filePath,
                'content' => $responseData['original_text'] ?? '',
                'preprocessed_content' => $responseData['preprocessed_text'] ?? '',
            ]);
    
            return response()->json([
                'message' => 'Reference document uploaded & preprocessed',
                'reference' => $reference,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    public function listReferences()
    {
        $references = ReferenceDocument::all();

        return response()->json([
            'message' => 'List of reference documents',
            'data' => $references
        ], 200);
    }   

    public function showUploadForm()
    {
        return view('upload');
    }
    

    
}


