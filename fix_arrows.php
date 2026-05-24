<?php

$files = [
    'resources/views/auth/login.blade.php',
    'resources/views/auth/register.blade.php',
    'resources/views/doctor/dashboard.blade.php',
    'resources/views/doctor/ideas/create.blade.php',
    'resources/views/doctor/ideas/edit.blade.php',
    'resources/views/doctor/ideas/index.blade.php',
    'resources/views/doctor/requests/index.blade.php',
    'resources/views/doctor/students/import.blade.php',
    'resources/views/doctor/students/index.blade.php',
    'resources/views/doctor/teams/create.blade.php',
    'resources/views/doctor/teams/distribute.blade.php',
    'resources/views/doctor/teams/edit.blade.php',
    'resources/views/doctor/teams/index.blade.php',
    'resources/views/doctor/teams/preview.blade.php',
    'resources/views/doctor/workspaces/index.blade.php',
    'resources/views/doctor/workspaces/phases/create.blade.php',
    'resources/views/doctor/workspaces/phases/edit.blade.php',
    'resources/views/doctor/workspaces/show.blade.php',
    'resources/views/doctor/workspaces/tasks/create.blade.php',
    'resources/views/doctor/workspaces/tasks/edit.blade.php',
    'resources/views/doctor/workspaces/tasks/show.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/student/activate.blade.php',
    'resources/views/student/dashboard.blade.php',
    'resources/views/student/login.blade.php',
    'resources/views/student/team.blade.php',
    'resources/views/student/workspace/show.blade.php',
    'resources/views/student/workspace/subtasks/show.blade.php',
    'resources/views/student/workspace/tasks/show.blade.php',
    'resources/views/vendor/pagination/cms.blade.php'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (!file_exists($path)) {
        echo "File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($path);

    // Chevron Right
    $content = preg_replace('/bi-chevron-right(?=[ \"\']|\Z)/', 'bi-chevron-{{ app()->getLocale() === \'ar\' ? \'left\' : \'right\' }}', $content);
    // Chevron Left
    $content = preg_replace('/bi-chevron-left(?=[ \"\']|\Z)/', 'bi-chevron-{{ app()->getLocale() === \'ar\' ? \'right\' : \'left\' }}', $content);
    
    // Arrow Right
    $content = preg_replace('/bi-arrow-right(?=[ \"\']|\Z)/', 'bi-arrow-{{ app()->getLocale() === \'ar\' ? \'left\' : \'right\' }}', $content);
    // Arrow Left
    $content = preg_replace('/bi-arrow-left(?=[ \"\']|\Z)/', 'bi-arrow-{{ app()->getLocale() === \'ar\' ? \'right\' : \'left\' }}', $content);

    // SPECIAL CASE — student/workspace/show.blade.php line 228
    if ($file === 'resources/views/student/workspace/show.blade.php') {
        $content = str_replace(
            "{{ \$phase->start_date->format('Y-m-d') }} → {{ \$phase->end_date->format('Y-m-d') }}",
            "{{ \$phase->start_date->format('Y-m-d') }} — {{ \$phase->end_date->format('Y-m-d') }}",
            $content
        );
    }

    file_put_contents($path, $content);
    echo "Updated $file\n";
}

echo "All arrows fixed.\n";
