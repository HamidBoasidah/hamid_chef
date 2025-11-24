<?php

use Illuminate\Filesystem\Filesystem;

require __DIR__ . '/../vendor/autoload.php';

$fs = new Filesystem();

// مسارات المصدر (من داخل مجلد vendor)
$stubsPath = __DIR__ . '/../vendor/laravel/breeze/stubs';
$inertiaCommon = $stubsPath . '/inertia-common';
$inertiaVue = $stubsPath . '/inertia-vue';
$default = $stubsPath . '/default';

// دالة مساعدة للنسخ الآمن
function safeCopy($source, $dest, $type = 'copy') {
    global $fs;
    
    if (!$fs->exists($source)) {
        echo "⚠️  Source not found: $source\n";
        return;
    }

    // إنشاء المجلد إذا لم يكن موجوداً
    $dir = dirname($dest);
    if (!$fs->isDirectory($dir)) {
        $fs->makeDirectory($dir, 0755, true);
    }

    // 1. إذا الملف غير موجود -> انسخه بالكامل
    if (!$fs->exists($dest)) {
        $fs->copy($source, $dest);
        echo "✅ Created: " . str_replace(base_path().'/', '', $dest) . "\n";
        return;
    }

    // 2. إذا الملف موجود
    echo "ℹ️  Exists: " . str_replace(base_path().'/', '', $dest) . " -> ";

    if ($type === 'append') {
        // دمج المحتوى في النهاية
        $currentContent = $fs->get($dest);
        $newContent = $fs->get($source);
        
        // تحقق بسيط لتجنب التكرار (اذا كان المحتوى موجود مسبقا لا تضفه)
        if (strpos($currentContent, 'Breeze') !== false || strpos($currentContent, 'auth.php') !== false) {
             echo "Skipped (Seems already merged)\n";
             return;
        }

        $fs->append($dest, "\n\n// --- BREEZE MERGED CONTENT START ---\n" . $newContent . "\n// --- BREEZE MERGED CONTENT END ---\n");
        echo "Merged (Appended)\n";

    } elseif ($type === 'side_by_side') {
        // إنشاء ملف جانبي للمقارنة اليدوية
        $info = pathinfo($dest);
        $newDest = $info['dirname'] . '/' . $info['filename'] . '.breeze.' . $info['extension'];
        $fs->copy($source, $newDest);
        echo "Created Side-by-Side File ($newDest)\n";
    
    } else {
        echo "Skipped (Type: $type)\n";
    }
}

function base_path($path = '') {
    return __DIR__ . '/../' . $path;
}

function resource_path($path = '') {
    return base_path('resources/' . $path);
}

function app_path($path = '') {
    return base_path('app/' . $path);
}

echo "🚀 Starting Safe Breeze Install...\n\n";

// 1. Controllers & Requests (Copy Directory - Merge logic handled by copyDirectory usually overwrites, so we iterate)
// سنقوم بنسخ الملفات الجديدة فقط، ولن نستبدل الموجودة
$controllersSource = $inertiaCommon . '/app/Http/Controllers';
foreach ($fs->allFiles($controllersSource) as $file) {
    $relativePath = $file->getRelativePathname();
    safeCopy($file->getPathname(), app_path('Http/Controllers/' . $relativePath), 'side_by_side'); 
    // استخدمنا side_by_side للكونترولرز لأننا لا نريد استبدال ملفات مثل Controller.php
}

$requestsSource = $default . '/app/Http/Requests';
foreach ($fs->allFiles($requestsSource) as $file) {
    $relativePath = $file->getRelativePathname();
    safeCopy($file->getPathname(), app_path('Http/Requests/' . $relativePath), 'copy'); 
    // Requests عادة جديدة، لذا copy آمن (لأنه يتحقق من الوجود أولاً)
}

// 2. Routes
safeCopy($inertiaCommon . '/routes/auth.php', base_path('routes/auth.php'), 'copy');
safeCopy($inertiaCommon . '/routes/web.php', base_path('routes/web.php'), 'append');

// 3. Views (Blade)
safeCopy($inertiaVue . '/resources/views/app.blade.php', resource_path('views/app.blade.php'), 'side_by_side');

// 4. JS Pages, Components, Layouts
// هذه المجلدات عادة تحتوي على ملفات جديدة، سننسخها. إذا وجد ملف بنفس الاسم لن يتم استبداله (بسبب شرط الـ copy في الدالة)
$jsDirs = ['Components', 'Layouts', 'Pages'];
foreach ($jsDirs as $dir) {
    $sourceDir = $inertiaVue . '/resources/js/' . $dir;
    if ($fs->exists($sourceDir)) {
        foreach ($fs->allFiles($sourceDir) as $file) {
            $relativePath = $file->getRelativePathname();
            safeCopy($file->getPathname(), resource_path('js/' . $dir . '/' . $relativePath), 'copy');
        }
    }
}

// 5. Config Files & App.js
safeCopy($inertiaVue . '/vite.config.js', base_path('vite.config.js'), 'side_by_side');
safeCopy($inertiaCommon . '/tailwind.config.js', base_path('tailwind.config.js'), 'side_by_side');
safeCopy($inertiaVue . '/resources/js/app.js', resource_path('js/app.js'), 'side_by_side');
safeCopy($default . '/resources/css/app.css', resource_path('css/app.css'), 'append');

echo "\n✅ Done! Please review the 'side_by_side' files (ending with .breeze.*) and merge them manually if needed.\n";
