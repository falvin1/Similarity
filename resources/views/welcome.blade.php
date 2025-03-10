<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Plagiarism Detection</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900">Advanced Plagiarism Detection</h1>
        <p class="text-gray-600 mt-2">Check your documents for plagiarism and ensure your content is original</p>
        
        <div class="mt-4 space-x-4">
            <a href="{{ route('login') }}" class="bg-black text-white px-4 py-2 rounded-md">Get Started →</a>
            <a href="{{ route('register') }}" class="bg-white text-black px-4 py-2 rounded-md border">Create Account</a>
            
        </div>
        
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center">
                <span class="text-blue-500 text-4xl">📄</span>
                <h3 class="text-lg font-semibold mt-2">PDF Upload</h3>
                <p class="text-gray-600 text-center">Upload PDF documents directly for plagiarism checking</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center">
                <span class="text-green-500 text-4xl">🛡</span>
                <h3 class="text-lg font-semibold mt-2">Secure Results</h3>
                <p class="text-gray-600 text-center">Your documents are processed securely and results are private</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center">
                <span class="text-purple-500 text-4xl">👤</span>
                <h3 class="text-lg font-semibold mt-2">User Accounts</h3>
                <p class="text-gray-600 text-center">Create an account to save your history and access advanced features</p>
            </div>
        </div>
    </div>
</body>
</html>