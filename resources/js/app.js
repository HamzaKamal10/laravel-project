import Alpine from 'alpinejs';

// نضع Alpine على كائن window حتى يمكن الوصول إليه من أدوات المتصفح عند الحاجة.
window.Alpine = Alpine;

// يبدأ Alpine في مراقبة مكونات x-data داخل صفحات Blade وتشغيل سلوكها التفاعلي.
Alpine.start();
