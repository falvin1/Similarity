<nav class="bg-white shadow-md p-4 flex justify-between items-center">
    <div class="text-lg font-bold flex items-center">
        <span class="text-blue-500 text-xl">📄</span>
        <span class="ml-2">PlagCheck</span>
    </div>
    <div class="flex items-center space-x-6">
        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-black">Dashboard</a>
        <a href="{{ route('history.index') }}" class="text-gray-600 hover:text-black">History</a>
        <div class="relative">
            <button id="userMenuButton" class="flex items-center space-x-2 focus:outline-none">
                <span class="text-gray-600">{{ Auth::user()->email }}</span>
                <span class="text-gray-600 text-xl">👤</span>
            </button>
            <div id="userMenu" class="hidden absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-md py-2">
                <a href="/profile" class="block px-4 py-2 text-gray-600 hover:bg-gray-200">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block px-4 py-2 text-gray-600 hover:bg-gray-200 w-full text-left">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>

