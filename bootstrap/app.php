<?php

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckOfficeApproval; // <-- 1. استدعاء الميدلوير الخاص بطلب موافقة المكاتب
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // 1. الميدلوير الذي يعمل تلقائياً مع كل طلبات الـ Web (Global Web Middleware)
        $middleware->web(append: [
            SetLocale::class,
        ]);

        // 2. تسجيل الأسماء المستعارة (Aliases) للاستخدام داخل ملف المسارات routes/web.php
        $middleware->alias([
            'set_locale'     => SetLocale::class,
            'admin'          => AdminMiddleware::class,
            'check.approval' => CheckOfficeApproval::class, // <-- 2. حل المشكلة: ربط الاسم المستعار 'check.approval' بالكلاس الخاص به
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();