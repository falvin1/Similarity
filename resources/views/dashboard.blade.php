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
            
            <form id="upload-form" action="{{ route('documents.uploadCheck') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                @csrf
                <div id="dropzone" class="border-dashed border-2 border-gray-300 rounded-lg p-6 text-center cursor-pointer">
                    <input type="file" name="file" class="hidden" id="file-upload" accept=".pdf">
                    <label for="file-upload" class="cursor-pointer block text-gray-500">Click to upload or drag and drop</label>
                    <p class="text-gray-400 text-sm">PDF files only (max 10MB)</p>
                    <p id="file-name" class="text-gray-700 mt-2 hidden"></p>
                </div>
                <div class="flex items-center mt-4">
                    <button type="submit" id="submit-button" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-md" disabled>
                        Check for Plagiarism
                    </button>
                    <div id="loadingIndicator" class="ml-4 hidden">
                        <svg class="w-6 h-6 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l4-4-4-4v4a8 8 0 100 16v-4l-4 4 4 4v-4a8 8 0 01-8-8z"></path>
                        </svg>
                    </div>
                </div>
            </form>

            @if ($errors->any())
                <div class="mt-4 p-4 bg-red-100 text-red-600 rounded">
                    <p><strong>Error:</strong> {{ $errors->first('file') }}</p>
                </div>
            @endif
            
            @if(session('result'))
            @php
            $result = session('result');
            $similarity = $result['highest_similarity']['similarity'] ?? 0;
        
            $statusColor = 'green';
            $statusText = 'Your content appears to be original';
            $statusMessage = 'Clean Document';
        
            if ($similarity >= 31 && $similarity <= 40) {
                $statusColor = 'yellow';
                $statusText = 'Your content might contain some duplicated content';
                $statusMessage = 'Suspicious Document';
            } elseif ($similarity >= 41) {
                $statusColor = 'red';
                $statusText = 'Your content appears to contain significant plagiarism';
                $statusMessage = 'Plagiarized Document';
            }
        
            if (empty($result['comparisons']) || count($result['comparisons']) === 0) {
                $topComparisons = collect([
                    [
                        'matched_text' => 'No excerpt available.',
                        'reference_title' => 'Default Source Title',
                        'google_drive_link' => $result['google_drive_link'] ?? '#',
                        'similarity' => 0
                    ]
                ]);
            } else {
                $topComparisons = collect($result['comparisons'])
                                    ->sortByDesc('similarity')
                                    ->take(1); // HANYA AMBIL 1 MATCH SAJA
            }
        @endphp
        

            <div class="mt-8 bg-white p-6 rounded-lg shadow-md mx-auto ">

                <h2 class="text-2xl font-semibold text-gray-800">
                    Results 
                    <span class="text-sm font-normal text-{{ $statusColor }}-600 ml-2">
                        {{ $similarity }}% similarity
                    </span>
                </h2>
                <p class="text-gray-500 mt-1 text-sm">Analysis of your content against our database</p>
                

                <div class="mt-5">
                    <h3 class="text-sm font-medium text-gray-600">Similarity Score</h3>
                    <div class="flex items-center gap-2 mt-2">
                        <p class="text-xs text-gray-400">0% (Original)</p>
                        <div class="flex-1 bg-gray-200 h-3 rounded-full">
                            <div
                                class="h-3 rounded-full transition-all duration-500 ease-in-out bg-{{ $statusColor }}-500"
                                style="width: {{ $similarity }}%"
                            ></div>
                        </div>
                        <p class="text-xs text-gray-400">100% (Plagiarized)</p>
                    </div>
                </div>
                

                <div class="mt-4 bg-{{ $statusColor }}-50 border border-{{ $statusColor }}-200 text-{{ $statusColor }}-600 p-4 rounded">
                    <p class="font-semibold">
                        @if($statusColor === 'green')
                            Clean Document
                        @elseif($statusColor === 'yellow')
                            Suspicious Document
                        @else
                            Plagiarized Document
                        @endif
                    </p>
                    <p class="text-sm mt-1">{{ $statusText }}</p>
                </div>
                
                <!-- POTENTIAL MATCHES -->
                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-4">Potential Matches</h3>
                @foreach ($topComparisons as $match)
                    @php
                        $simValue = $match['similarity'] ?? 0;
        

                        $percentageColor = 'green';
                        if ($simValue > 40) {
                            $percentageColor = 'red';
                        } elseif ($simValue > 30) {
                            $percentageColor = 'yellow';
                        }

                        // Jika matched_text kosong atau "No excerpt available.",
                        // gunakan reference_title sebagai fallback
                        $displayText = trim($match['matched_text'] ?? '');
                        if (!$displayText || strtolower($displayText) === 'no excerpt available.') {
                            $displayText = $match['reference_title'] ?? 'No excerpt found.';
                        }
                    @endphp
                    <div class="border p-4 rounded mb-4">
                        <h4 class="text-lg font-bold">Potential Match</h4>
                        <p class="text-sm text-gray-600 italic mt-1">
                            "{{ $displayText }}"
                        </p>
                        <p class="mt-2 text-sm text-gray-500">
                            Potential source:
                            @if(session('result') && !empty(session('result')['google_drive_link']))
                                <a 
                                    href="{{ session('result')['google_drive_link'] }}" 
                                    target="_blank"
                                    class="text-blue-600 underline hover:text-blue-800 transition-colors duration-200"
                                >
                                    View on Google Drive
                                </a>
                            @else
                                <span class="text-gray-400 italic">No link available.</span>
                            @endif
                        </p>
                    </div>
                @endforeach
                
                <!-- INFORMASI TAMBAHAN -->
                <p class="mt-6 text-sm text-gray-600">
                    This is a demonstration. A real plagiarism checker would use advanced algorithms to compare your content against a vast database of sources.
                </p>
                
                <!-- TOMBOL CHECK ANOTHER -->
                <div class="mt-6">
                    <a href="{{ route('dashboard') }}"
                       class="inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
                    >
                        Check Another Document
                    </a>
                </div>
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
        const loadingIndicator = document.getElementById('loadingIndicator');
        const form = document.getElementById('upload-form');

        fileUpload.addEventListener('change', function () {
            if (fileUpload.files.length > 0) {
                fileNameDisplay.textContent = `Selected file: ${fileUpload.files[0].name}`;
                fileNameDisplay.classList.remove('hidden');
                submitButton.disabled = false;
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
                submitButton.disabled = false;
            }
        });

        form.addEventListener('submit', function () {
            submitButton.disabled = true;
            submitButton.innerText = 'Checking...';
            loadingIndicator.classList.remove('hidden');
        });
    </script>
</body>
</html>
