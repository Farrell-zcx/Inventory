<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - INVENTORY</title>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@600;700;900&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface font-body-lg flex items-center justify-center h-screen selection:bg-primary-container selection:text-black">
    <div class="bg-surface p-8 rounded-xl shadow-lg max-w-md w-full text-center border border-outline-variant/30 flex flex-col items-center">
        <span class="material-symbols-outlined text-error text-6xl mb-4" style="font-variation-settings: 'FILL' 1;">gpp_bad</span>
        
        <h1 class="text-2xl font-bold text-on-surface mb-2 tracking-tight font-headline-md">Akses Ditolak (403)</h1>
        
        <p class="text-on-surface-variant mb-8 text-sm">
            {{ $exception->getMessage() ?: 'Anda belum memiliki akses (Role) di aplikasi Inventory. Silakan hubungi Administrator IT untuk melakukan Provisioning.' }}
        </p>
        
        <a href="{{ config('services.sso.base_url') }}/logout-web?redirect_to={{ urlencode(url('/')) }}" class="bg-primary hover:bg-primary/90 text-on-primary py-3 px-6 rounded-lg font-bold transition-colors w-full flex items-center justify-center gap-2">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali ke Login SSO
        </a>
    </div>
</body>
</html>
