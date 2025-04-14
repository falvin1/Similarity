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
                                <th class="py-3 px-4 text-left">User</th>
                                <th class="py-3 px-4 text-left">Email</th>
                                <th class="py-3 px-4 text-left">Role</th>
                                <th class="py-3 px-4 text-left">Created</th>
                                <th class="py-3 px-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="border-t hover:bg-gray-50 transition">
                                    <td class="py-3 px-4">{{ $user->name }}</td>
                                    <td class="py-3 px-4">{{ $user->email }}</td>
                                    <td class="py-3 px-4">
                                    @if($user->role === 'admin')
                                        <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded text-sm">Admin</span>
                                    @else
                                        <span class="bg-gray-200 px-2 py-1 rounded text-sm">User</span>
                                    @endif
                                    </td>
                                    <td class="py-3 px-4">{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
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
