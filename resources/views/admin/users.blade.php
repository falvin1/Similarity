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
                                                 x-cloak>
                                                <div class="py-2 px-4 text-sm font-medium text-gray-700 border-b">
                                                    Actions
                                                </div>
                                                
                                                <!-- Edit User Link -->
                                                <button @click="$dispatch('open-modal', 'edit-user-{{ $user->id }}'); open = false" 
                                                        class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit User
                                                </button>
                                    
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('apakah anda yakin?')">
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
                                    
                                        <!-- Edit User Modal -->
                                        <x-modal name="edit-user-{{ $user->id }}" :show="false" focusable>
                                            <div class="p-6">
                                                <div class="flex justify-between mb-1">
                                                    <h2 class="text-lg font-semibold">Edit User</h2>
                                                </div>
                                                <div class="flex justify-between mb-1">
                                                    <p class="text-gray-500 mb-4">Update user information</p>
                                                </div>
                                    
                                                <form action="{{ route('users.update', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <div class="mb-4">
                                                        <label for="name-{{ $user->id }}" class="text-sm font-medium text-gray-700">Full Name</label>
                                                        <input type="text" id="name-{{ $user->id }}" name="name" value="{{ $user->name }}" 
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-300">
                                                    </div>
                                                    
                                                    <div class="mb-4">
                                                        <label for="email-{{ $user->id }}" class="text-sm font-medium text-gray-700">Email</label>
                                                        <input type="email" id="email-{{ $user->id }}" name="email" value="{{ $user->email }}" 
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-300">
                                                    </div>
                                                    
                                                    <div class="mb-4">
                                                        <label for="role-{{ $user->id }}" class="text-sm font-medium text-gray-700">Role</label>
                                                        <select id="role-{{ $user->id }}" name="role" 
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-300">
                                                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="mb-4">
                                                        <label for="password-{{ $user->id }}" class="text-sm font-medium text-gray-700">Reset Password (optional)</label>
                                                        <input type="password" id="password-{{ $user->id }}" name="password" placeholder="Leave blank to keep current password" 
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-300">
                                                    </div>
                                                    
                                                    <div class="flex justify-end gap-3 mt-6">
                                                        <button type="button" @click="$dispatch('close')" 
                                                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                                            Cancel
                                                        </button>
                                                        <button type="submit" 
                                                            class="px-4 py-2 bg-gray-800 border border-transparent rounded-md text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                            Update User
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </x-modal>
                                    </td>
                                    @endforeach     
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>