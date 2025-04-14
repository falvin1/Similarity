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
                <h1 class="text-2xl font-bold mb-2">Users</h1>
                <p class="text-gray-600 mb-6">Manage user accounts and permissions</p>
                
                <!-- Search & Actions -->
                <div class="flex flex-wrap gap-4 items-center mb-6">
                    <input type="text" placeholder="Search users..." class="flex-1 px-4 py-2 border rounded-md text-gray-700 focus:ring focus:ring-blue-300">
                </div>

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
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Yakin mau menghapus dokumen ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M6 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                                                <path fill-rule="evenodd" d="M4 5a1 1 0 011-1h10a1 1 0 011 1v1H4V5zm2 2h8v9a2 2 0 01-2 2H8a2 2 0 01-2-2V7z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
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
