<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="flex h-screen">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1 p-6 overflow-y-auto">
            <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-lg">
                <h1 class="text-2xl font-bold mb-2">Documents</h1>
                <p class="text-gray-600 mb-6">Manage Documents and their plagiarism results</p>
                
                <!-- User Table -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse bg-white shadow-md rounded-lg">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="py-3 px-4">Document</th>
                                <th class="py-3 px-4">User</th>
                                <th class="py-3 px-4">Similarity</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documents as $doc)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8H6a2 2 0 01-2-2V6a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ $doc->title }}
                                </td>
                                <td class="py-3 px-4">{{ $doc->user->name }}</td>
                                <td class="py-3 px-4">
                                    <span class="text-sm font-semibold text-{{ $doc->status_color }}-700">
                                        {{ $doc->similarity_percentage }}%
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-{{ $doc->status_color }}-100 text-{{ $doc->status_color }}-700">
                                        {{ ucfirst($doc->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">{{ $doc->created_at->format('n/j/Y') }}</td>
                                <td class="py-3 px-4 text-right text-center relative">
                                    <div class="dropdown inline-block relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                        <div x-show="open" 
                                             @click.away="open = false"
                                             class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200"
                                             style="display: none;">
                                            <div class="py-2 px-4 text-sm font-medium text-gray-700 border-b">
                                                Actions
                                            </div>

                                            <a href="{{ route('documents.download', $doc->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Download
                                            </a>
                                            <form action="{{ route('documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Yakin mau menghapus dokumen ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>  
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>