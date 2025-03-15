<!DOCTYPE html>
<html class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .shake { animation: shake 0.5s ease-in-out; }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-red-50 to-orange-50">
    <div class="min-h-screen px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8">
        <div class="max-w-max mx-auto">
            <main class="sm:flex">
                <div class="text-center sm:text-left">
                    <div class="sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-4xl font-extrabold text-red-600 sm:text-5xl">403</p>
                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Akses Ditolak</h1>
                            <p class="mt-4 text-base text-gray-500">Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.</p>
                        </div>
                        <div class="mt-8 sm:mt-0">
                            <img src="https://illustrations.popsy.co/red/security.svg" alt="403 Illustration" class="w-64 h-64 shake">
                        </div>
                    </div>

                    <div class="mt-10 flex flex-col sm:flex-row gap-4">
                        <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-red-600 hover:bg-red-700 transition-colors duration-200">
                            <svg class="mr-2 -ml-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali
                        </a>
                    </div>

                    <div class="mt-10">
                        <div class="inline-flex items-center px-4 py-2 rounded-full text-sm text-gray-500 bg-gray-100 ring-1 ring-inset ring-gray-300/20">
                            <svg class="mr-2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Butuh bantuan? Hubungi (021) 123-4567
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
