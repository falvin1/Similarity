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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Smalot\PdfParser\Parser; // Import PDF Parser

class DocumentController extends Controller
{


    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:20480',
        ]);
    
        Log::info('File received:', ['filename' => $request->file('file')->getClientOriginalName()]);
        $file = $request->file('file');
        $filePath = $file->store('documents', 'public');
    
        try {
    
            $response = Http::withoutVerifying()->attach(
                'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
            )->post('http://127.0.0.1:5000/preprocess');
    
            if ($response->failed()) {
                return redirect()->back()->with('error', 'Gagal memproses file dengan Flask');
            }
    
            $responseData = $response->json();
    
      
            $document = Document::create([
                'user_id' => Auth::id(), 
                'title' => $file->getClientOriginalName(),
                'content' => $responseData['original_text'] ?? '',
                'preprocessed_content' => $responseData['preprocessed_text'] ?? '',
                'file_path' => $filePath,
            ]);
    
            
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
         

            
            return redirect()->back()->with('result', [
                'highest_similarity' => $highest,
                'comparisons' => $comparisons,
                'google_drive_link' => $googleDriveLink ?? null,
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }    
    public function adminPage($page)
    {
        
        $totalDocuments = Document::count();
        $cleanDocuments = Document::where('similarity_percentage', '<=', 30)->count();
        $suspiciousDocuments = Document::whereBetween('similarity_percentage', [31, 40])->count();
        $plagiarizedDocuments = Document::where('similarity_percentage', '>', 40)->count();
        
        $documents = Document::latest()->take(3)->get();
        $data = [
            'totalDocuments' => $totalDocuments,
            'cleanDocuments' => $cleanDocuments,
            'suspiciousDocuments' => $suspiciousDocuments,
            'plagiarizedDocuments' => $plagiarizedDocuments,
            'documents'=>$documents,
        ];
        if ($page === 'dashboard') {
            return view('admin.dashboard', $data);
        } elseif ($page === 'upload') {
            return view('admin.upload', $data);
        }
    
        abort(404);
    }
    public function documentPage(){
        $documents = Document::with('user')->latest()->get();


        return view('admin.documents', compact('documents'));
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);

        // Hapus file dari penyimpanan
        Storage::delete($document->file_path);

        // Hapus dari database
        $document->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }
    public function list()
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
    public function download($id)
    {
        $document = Document::findOrFail($id); // Cari dokumen berdasarkan ID
        Log::info('Document Path: ' . $document->file_path); // Menambahkan log untuk memeriksa path
    
        // Menggunakan public_path untuk file yang ada di folder storage/public
        $filePath = public_path('storage/' . $document->file_path); 
    
        Log::info('Full File Path: ' . $filePath); // Log full path untuk debugging
    
        // Cek apakah file ada di penyimpanan
        if (file_exists($filePath)) {
            return response()->download($filePath);
        } else {
            return abort(404, 'File not found');
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


