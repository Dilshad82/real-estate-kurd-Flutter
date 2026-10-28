<footer class="bg-slate-950 text-slate-400 py-10 border-t border-slate-900 text-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
        
        <!-- حقوق النشر وتحديث السنة تلقائياً -->
        <p>© {{ date('Y') }} {{ app()->getLocale() == 'ku' ? 'پلاتفۆڕما عەقاراتێن من. هەمی ماف پاراستینە.' : 'منصة عقاراتي. جميع الحقوق محفوظة.' }}</p>
        
        <!-- روابط الفوتر المفعلة -->
        <div class="flex items-center gap-6">
            <a href="{{ url('/about') }}" class="hover:text-amber-400 transition">
                {{ app()->getLocale() == 'ku' ? 'دربارەی مە' : 'عن المنصة' }}
            </a>
            <a href="{{ url('/terms') }}" class="hover:text-amber-400 transition">
                {{ app()->getLocale() == 'ku' ? 'مەرج و ڕێنمایی' : 'الشروط والأحكام' }}
            </a>
            <a href="{{ url('/privacy') }}" class="hover:text-amber-400 transition">
                {{ app()->getLocale() == 'ku' ? 'سياسەتا تایبەتمەنديێ' : 'سياسة الخصوصية' }}
            </a>
            <a href="{{ url('/contact') }}" class="hover:text-amber-400 transition">
                {{ app()->getLocale() == 'ku' ? 'پەیوەندی' : 'اتصل بنا' }}
            </a>
        </div>

    </div>
</footer>