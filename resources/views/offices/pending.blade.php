<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بانتظار الموافقة</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-md text-center max-w-md w-full border border-gray-200">
        <div class="text-amber-500 text-6xl mb-4">⏳</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">حسابك قيد المراجعة</h1>
        <p class="text-gray-600 mb-6 leading-relaxed">
            مرحباً بك! تم تسجيل مكتبك بنجاح وهو الآن بانتظار موافقة وتفعيل مدير النظام. يمكنك العودة وتصفح لوحة التحكم بمجرد اعتماد حسابك.
        </p>
        <a href="{{ route('force.logout') }}" class="inline-block px-6 py-2.5 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 transition shadow">
            تسجيل الخروج
        </a>
    </div>
</body>
</html>