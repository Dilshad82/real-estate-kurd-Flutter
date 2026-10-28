<!DOCTYPE html>
<html lang="{{ app()->getLocale() == 'ku' ? 'ckb' : 'ar' }}" dir="rtl" class="notranslate" translate="no">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google" content="notranslate">

    <title>{{ config('app.name', 'منصة عقارات إقليم كردستان') }}</title>

    {{-- 1. استدعاء أصول Vite أولاً --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- 2. جلب خطوط Google --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    {{-- 3. Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    {{-- 4. إجبار المتصفح كلياً بعد تحميل كل المكونات على تطبيق خط Cairo وتوصيل الحروف --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap');

        html, body, body *, [class*="font-"] {
            font-family: 'Cairo', sans-serif !important;
            letter-spacing: 0 !important;
            word-spacing: normal !important;
            font-feature-settings: "liga" 1, "calt" 1, "kern" 1 !important;
            text-rendering: optimizeLegibility !important;
        }

        .tracking-tight, .tracking-tighter, .tracking-wide, .tracking-wider, .tracking-widest {
            letter-spacing: 0 !important;
        }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-800 selection:bg-indigo-500 selection:text-white" dir="rtl">
    
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>
    </div>

</body>
</html>