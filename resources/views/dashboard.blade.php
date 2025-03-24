<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PlagCheck</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <x-navbar />

    
    <div class="p-6">
        <h1 class="text-2xl font-bold">Welcome, {{ Auth::user()->name }}</h1>
        <p class="text-gray-600">Upload a document or paste text to check for plagiarism</p>
        
        <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold">Upload PDF</h2>
            <p class="text-gray-600">Upload a PDF document to check for plagiarism</p>
            
            <form action="{{ route('documents.uploadCheck') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                @csrf
                <div id="dropzone" class="border-dashed border-2 border-gray-300 rounded-lg p-6 text-center cursor-pointer">
                    <input type="file" name="file" class="hidden" id="file-upload" accept=".pdf">
                    <label for="file-upload" class="cursor-pointer block text-gray-500">Click to upload or drag and drop</label>
                    <p class="text-gray-400 text-sm">PDF files only (max 10MB)</p>
                    <p id="file-name" class="text-gray-700 mt-2 hidden"></p>
                </div>
                <button type="submit" id="submit-button" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-md" disabled>Check for Plagiarism</button>
            </form>
            
            @if ($errors->any())
                <div class="mt-4 p-4 bg-red-100 text-red-600 rounded">
                    <p><strong>Error:</strong> {{ $errors->first('file') }}</p>
                </div>
            @endif
            
            @if(session('result'))
                <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900">Hasil Pemeriksaan Plagiarisme</h3>
                    <p class="text-gray-700 mt-2"><strong>Similarity Tertinggi:</strong> {{ session('result')['highest_similarity']['similarity'] }}%</p>
                    <p class="text-gray-700"><strong>Referensi dengan Similarity Tertinggi:</strong> {{ session('result')['highest_similarity']['reference_title'] }}</p>
                   
                        <p class="text-gray-700 mt-2"><strong>Link Google Drive:</strong> 
                            <a href="{{ optional(session('result'))['google_drive_link'] }}" class="text-blue-500 underline" target="_blank">Lihat Dokumen</a>

                    
                    
                    <h4 class="text-gray-800 font-semibold mt-4">Perbandingan dengan Referensi:</h4>
                    <ul class="list-disc list-inside text-gray-700">
                        @foreach(session('result')['comparisons'] as $comparison)
                            <li>{{ $comparison['reference_title'] }} - {{ $comparison['similarity'] }}%</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    </div>
    
    <script>
        document.getElementById('userMenuButton').addEventListener('click', function () {
            document.getElementById('userMenu').classList.toggle('hidden');
        });
        
        const fileUpload = document.getElementById('file-upload');
        const fileNameDisplay = document.getElementById('file-name');
        const submitButton = document.getElementById('submit-button');
        const dropzone = document.getElementById('dropzone');
        
        fileUpload.addEventListener('change', function () {
            if (fileUpload.files.length > 0) {
                fileNameDisplay.textContent = `Selected file: ${fileUpload.files[0].name}`;
                fileNameDisplay.classList.remove('hidden');
                submitButton.removeAttribute('disabled');
            }
        });
        
        dropzone.addEventListener('dragover', function (event) {
            event.preventDefault();
            dropzone.classList.add('bg-gray-200');
        });
        
        dropzone.addEventListener('dragleave', function () {
            dropzone.classList.remove('bg-gray-200');
        });
        
        dropzone.addEventListener('drop', function (event) {
            event.preventDefault();
            dropzone.classList.remove('bg-gray-200');
            
            if (event.dataTransfer.files.length > 0) {
                fileUpload.files = event.dataTransfer.files;
                fileNameDisplay.textContent = `Selected file: ${fileUpload.files[0].name}`;
                fileNameDisplay.classList.remove('hidden');
                submitButton.removeAttribute('disabled');
            }
        });
    </script>
</body>
</html>
