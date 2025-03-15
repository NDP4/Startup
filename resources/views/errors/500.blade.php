<!DOCTYPE html>
<html class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Kesalahan Server</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .spin { animation: spin 20s linear infinite; }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="min-h-screen px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8">
        <div class="max-w-max mx-auto">
            <main class="sm:flex">
                <div class="text-center sm:text-left">
                    <div class="sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-4xl font-extrabold text-gray-600 sm:text-5xl">500</p>
                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Kesalahan Server</h1>
                            <p class="mt-4 text-base text-gray-500">Maaf, telah terjadi kesalahan pada server kami. Tim teknis kami sedang menangani masalah ini.</p>
                        </div>
                        <div class="mt-8 sm:mt-0">
                            <img src="https://illustrations.popsy.co/gray/server.svg" alt="500 Illustration" class="w-64 h-64 spin">
                        </div>
                    </div>

                    <div class="mt-10 flex flex-col sm:flex-row gap-4">
                        <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gray-600 hover:bg-gray-700 transition-colors duration-200">
                            <svg class="mr-2 -ml-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Kembali ke Beranda
                        </a>
                    </div>

                    <div class="mt-10">
                        <div class="inline-flex items-center px-4 py-2 rounded-full text-sm text-gray-500 bg-gray-100 ring-1 ring-inset ring-gray-300/20">
                            <svg class="mr-2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Tim teknis kami telah diberitahu tentang masalah ini
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
