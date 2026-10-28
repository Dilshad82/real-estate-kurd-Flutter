<?php
    namespace App\Helpers;

    class WhatsappHelper
    {
        public static function generateClientContactUrl(string $phone, string $clientName, string $propertyTitle): string
        {
            // تنظيف رقم الهاتف وإضافة الرمز الدولي لـ العراق (+964)
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '964' . substr($cleanPhone, 1);
            }

            $message = "مرحباً {$clientName}، نحن مكتب عقاري ولدينا عقار يطابق طلبك تماماً: ({$propertyTitle}). هل تفضل التحدث الآن؟";

            return "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
        }
    }