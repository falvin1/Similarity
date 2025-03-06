<?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\ReferenceDocument;

class GoogleDriveController extends Controller
{
    public function downloadAndSaveToDatabase()
    {
        $path = storage_path('google-drive-credentials.json');

        if (!file_exists($path)) {
            return response()->json(['error' => 'File Service Account tidak ditemukan'], 404);
        }

        try {
            // Setup Google Client
            $client = new Client();
            $client->setAuthConfig($path);
            $client->addScope(Drive::DRIVE_READONLY);

            // Inisialisasi service Google Drive
            $service = new Drive($client);

            // Ambil daftar semua file di Google Drive
            $files = $service->files->listFiles()->getFiles();

            if (empty($files)) {
                return response()->json(['message' => 'Tidak ada file di Google Drive.']);
            }

            $savedFiles = [];
            $skippedFiles = [];

            foreach ($files as $file) {
                if ($file->getMimeType() === 'application/pdf') {
                    $fileId = $file->getId();
                    $fileName = $file->getName();

                    // 🔍 Cek apakah file sudah ada di database dan telah diproses sebelumnya
                    $existingFile = ReferenceDocument::where('title', $fileName)
                        ->whereNotNull('preprocessed_content')
                        ->first();

                    if ($existingFile) {
                        $skippedFiles[] = $fileName; // Tambahkan ke daftar file yang dilewati
                        continue; // Skip ke file berikutnya
                    }

                    // 📥 Download file dari Google Drive
                    $http = $client->authorize();
                    $response = $http->request('GET', "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media");

                    // Simpan file ke storage Laravel
                    $savePath = 'reference_documents/' . $fileName;
                    Storage::disk('public')->put($savePath, $response->getBody()->getContents());

                    $filePath = storage_path('app/public/' . $savePath);

                    // 📤 Kirim file ke Flask API untuk preprocessing
                    $response = Http::attach(
                        'file', fopen($filePath, 'r'), $fileName
                    )->post('http://127.0.0.1:5000/preprocess');

                    if ($response->failed()) {
                        return response()->json([
                            'error' => 'Gagal memproses file dengan Flask'
                        ], 500);
                    }

                    $responseData = $response->json();

                    // 💾 Simpan ke database
                    $reference = ReferenceDocument::updateOrCreate(
                        ['title' => $fileName],
                        [
                            'file_path' => $savePath,
                            'content' => $responseData['original_text'] ?? '',
                            'preprocessed_content' => $responseData['preprocessed_text'] ?? '',
                        ]
                    );

                    $savedFiles[] = $reference;
                }
            }

            return response()->json([
                'message' => 'Files processed successfully',
                'processed_files' => $savedFiles,
                'skipped_files' => $skippedFiles,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
