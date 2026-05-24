<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$students = \App\Models\Student::all();
$updated = 0;

foreach ($students as $student) {
    $original = $student->name;
    $normalized = $original;

    // Use Intl Normalizer if available
    if (class_exists('Normalizer')) {
        $normalized = Normalizer::normalize($original, Normalizer::FORM_KC);
    } else {
        // Fallback for the specific presentation forms mentioned
        $replacements = [
            'ﯾ' => 'ي',
            'ﻮ' => 'و',
            'ﺳ' => 'س',
            'ﻒ' => 'ف',
            'ي' => 'ي'
        ];
        $normalized = strtr($original, $replacements);
    }

    if ($normalized !== $original) {
        $student->name = $normalized;
        $student->save();
        $updated++;
    }
}

echo "Normalized $updated student names.\n";
