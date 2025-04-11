<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Plagiarism Detection System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <style>
    .spin {
      animation: spin 1s linear infinite;
    }
  
    @keyframes spin {
      from {
        transform: rotate(0deg);
      }
      to {
        transform: rotate(360deg);
      }
    }
  </style>
</head>
<body class="bg-gray-100">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <x-sidebar />
    
    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto">
      <div class="p-8 main-content">
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-600 mt-1">Overview of plagiarism detection system</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
          <!-- Total Documents Card -->
         <!-- Total Documents Card -->
          <div class="bg-white p-6 rounded-md shadow-sm border border-gray-100">
            <div class="flex justify-between items-center">
              <div>
                <p class="text-gray-600">Total Documents</p>
                <h2 class="text-4xl font-bold text-gray-800 mt-2">{{ $totalDocuments }}</h2>
              </div>
              <div class="bg-blue-100 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
          </div>
          
          <!-- Clean Documents Card -->
          <div class="bg-white p-6 rounded-md shadow-sm border border-gray-100">
            <div class="flex justify-between items-center">
              <div>
                <p class="text-gray-600">Clean Documents</p>
                <h2 class="text-4xl font-bold text-green-600 mt-2">{{ $cleanDocuments }}</h2>
              </div>
              <div class="bg-green-100 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>
          
          <!-- Suspicious Documents Card -->
          <div class="bg-white p-6 rounded-md shadow-sm border border-gray-100">
            <div class="flex justify-between items-center">
              <div>
                <p class="text-gray-600">Suspicious Documents</p>
                <h2 class="text-4xl font-bold text-amber-500 mt-2">{{ $suspiciousDocuments }}</h2>
              </div>
              <div class="bg-amber-100 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
            </div>
          </div>
          
          
          <!-- Plagiarized Documents Card -->
          <div class="bg-white p-6 rounded-md shadow-sm border border-gray-100">
            <div class="flex justify-between items-center">
              <div>
                <p class="text-gray-600">Plagiarized Documents</p>
                <h2 class="text-4xl font-bold text-red-600 mt-2">3</h2>
              </div>
              <div class="bg-red-100 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <div class="mt-8">
          <div class="border-b border-gray-200">
            <nav class="-mb-px flex">
              <a href="dashboard" class="        border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                Recent Activity
              </a>
              <a href="upload" class="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                Uploads
              </a>
            </nav>
          </div>
        </div>
        
        <!-- Recent Documents -->
        <div class="mt-6 bg-white rounded-md shadow-sm border border-gray-100 p-6">
          <h2 class="text-xl font-bold text-gray-800">Recent Document Submissions</h2>
          <p class="text-gray-600 text-sm mt-1">The latest documents submitted for plagiarism checking</p>
          
          <div class="mt-6 space-y-6">
            <!-- Document 1 -->
            <div class="flex items-center gap-5">
                <button id="downloadFilesBtn" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Download Files from Google Drive
                </button>
                
                <div id="loadingIndicator" class="ml-4 hidden">
                  <svg class="w-9 h-9 spin" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="#000000"/>
                    <path d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z" fill="#000000"/>
                  </svg>
              </div>
              </div>
            </div>

            

            
          </div>
        </div>
      </div>
    </div>
</body>

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script>
    function showSettings() {
        // Sembunyikan konten lain (misalnya dashboard)
        document.querySelectorAll('.main-content').forEach(el => el.classList.add('hidden'));
        
        // Tampilkan konten Settings
        document.getElementById('settings-content').classList.remove('hidden');
    }

document.getElementById('downloadFilesBtn').addEventListener('click', function() {
  let button = this;
    let loadingIndicator = document.getElementById('loadingIndicator');

    // Disable button and show loading spinner
    button.disabled = true;
    button.innerText = "Downloading...";
    loadingIndicator.classList.remove('hidden');

    fetch("{{ route('download.files') }}")
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            Toastify({
              text: "Success: " + data.message,
              duration: 3000,
              newWindow: true,
              close: true,
              gravity: "top",
              position: "right",
              stopOnFocus: true,
              style: {
                background: "linear-gradient(to right, #00b09b, #96c93d)",
              }
            }).showToast();
        } else if (data.error) {
          Toastify({
              text: "Error: " + data.message,
              duration: 3000,
              newWindow: true,
              close: true,
              gravity: "top",
              position: "right",
              stopOnFocus: true,
              style: {
                background: "linear-gradient(to right, #00b09b, #96c93d)",
              }
            }).showToast();
        }
    })
    .catch(error => {
        alert("Something went wrong!");
        console.error(error);
    }).finally(() => {
      button.disabled = false;
        button.innerText = "Download Files from Google Drive";
        loadingIndicator.classList.add('hidden');
    });
});

</script>

