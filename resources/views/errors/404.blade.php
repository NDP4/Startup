<!DOCTYPE html>
<html class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .floating { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-blue-50 to-indigo-50">
    <div class="min-h-screen px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8">
        <div class="mx-auto max-w-max">
            <main class="sm:flex">
                <div class="text-center sm:text-left">
                    <div class="sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-4xl font-extrabold text-blue-600 sm:text-5xl">404</p>
                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Halaman Tidak Ditemukan</h1>
                            <p class="mt-4 text-base text-gray-500">Maaf, kami tidak dapat menemukan halaman yang Anda cari.</p>
                        </div>
                        <div class="mt-8 sm:mt-0">
                            <img src="https://illustrations.popsy.co/blue/falling.svg"
                                 alt="404 Illustration"
                                 class="w-64 h-64 floating">
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 mt-10 sm:flex-row">
                        <a href="{{ url()->previous() }}"
                           class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white transition-colors duration-200 bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali
                        </a>
                        <a href="{{ route('filament.panel.resources.bookings.index') }}"
                           class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-blue-700 transition-colors duration-200 bg-transparent border-2 border-blue-600 rounded-xl hover:bg-blue-50">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Ke Dashboard
                        </a>
                    </div>

                    <div class="mt-10">
                        <div class="inline-flex items-center px-4 py-2 text-sm text-gray-500 bg-gray-100 rounded-full ring-1 ring-inset ring-gray-300/20">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
