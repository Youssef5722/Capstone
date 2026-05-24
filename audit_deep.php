<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$viewsPath = __DIR__ . '/resources/views';
$langPathEn = __DIR__ . '/lang/en/cms.php';
$langEn = require $langPathEn;

function keyExists($key, $array) {
    $parts = explode('.', $key);
    // remove 'cms' prefix
    if ($parts[0] === 'cms') array_shift($parts);
    
    $current = $array;
    foreach ($parts as $part) {
        if (!is_array($current) || !array_key_exists($part, $current)) {
            return false;
        }
        $current = $current[$part];
    }
    return true;
}

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));
$files = [];
foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $files[] = $file->getPathname();
    }
}

$report = "# PATCH B DEEP AUDIT\n\n";

// 1. ARROW ICONS
$report .= "## 1. ARROW ICONS (Hardcoded)\n";
$arrowsFound = false;
foreach ($files as $file) {
    $lines = file($file);
    $relPath = str_replace($viewsPath . DIRECTORY_SEPARATOR, '', $file);
    foreach ($lines as $i => $line) {
        if (preg_match('/(bi-arrow-(right|left)|bi-chevron-(right|left)|→|←|fa-arrow-)/', $line)) {
            if (!preg_match('/app\(\)->getLocale\(\)/', $line)) {
                $report .= "- **File:** " . $relPath . "\n  **Line " . ($i+1) . ":** `" . trim($line) . "`\n\n";
                $arrowsFound = true;
            }
        }
    }
}
if (!$arrowsFound) $report .= "None found.\n\n";

// 2. INPUT FIELD ICONS
$report .= "## 2. INPUT FIELD ICONS\n";
$iconsFound = false;
foreach ($files as $file) {
    $lines = file($file);
    $relPath = str_replace($viewsPath . DIRECTORY_SEPARATOR, '', $file);
    foreach ($lines as $i => $line) {
        if (preg_match('/(input-icon-wrapper|prefix-icon|position:\s*absolute.*?left:|position:\s*absolute.*?right:)/', $line)) {
            $report .= "- **File:** " . $relPath . "\n  **Line " . ($i+1) . ":** `" . trim($line) . "`\n\n";
            $iconsFound = true;
        }
    }
}
if (!$iconsFound) $report .= "None found.\n\n";

// 3. PASSWORD TOGGLE BUTTONS
$report .= "## 3. PASSWORD TOGGLE BUTTONS\n";
$pwFound = false;
foreach ($files as $file) {
    $lines = file($file);
    $relPath = str_replace($viewsPath . DIRECTORY_SEPARATOR, '', $file);
    foreach ($lines as $i => $line) {
        if (preg_match('/(btn-toggle-password|bi-eye)/', $line)) {
            $report .= "- **File:** " . $relPath . "\n  **Line " . ($i+1) . ":** `" . trim($line) . "`\n\n";
            $pwFound = true;
        }
    }
}
if (!$pwFound) $report .= "None found.\n\n";

// 4. MISSING TRANSLATION KEYS
$report .= "## 4. MISSING TRANSLATION KEYS\n";
$keysFound = false;
foreach ($files as $file) {
    $content = file_get_contents($file);
    $relPath = str_replace($viewsPath . DIRECTORY_SEPARATOR, '', $file);
    
    preg_match_all('/__\([\'"](cms\.[^\'"]+)[\'"]\)/', $content, $matches, PREG_OFFSET_CAPTURE);
    foreach ($matches[1] as $match) {
        $key = $match[0];
        $offset = $match[1];
        
        $missing = !keyExists($key, $langEn);
        $deep = count(explode('.', $key)) > 3; // cms + 2 levels = 3 parts. More than 2 levels means > 3 parts.
        
        if ($missing || $key === 'cms.student.name' || $deep) {
            // Find line number
            $lineNum = substr_count(substr($content, 0, $offset), "\n") + 1;
            $reason = [];
            if ($missing) $reason[] = "Missing in lang/en/cms.php";
            if ($key === 'cms.student.name') $reason[] = "Specifically requested check";
            if ($deep) $reason[] = "More than 2 levels deep";
            
            $report .= "- **File:** " . $relPath . " (Line " . $lineNum . ")\n  **Key:** `" . $key . "`\n  **Reason:** " . implode(', ', $reason) . "\n\n";
            $keysFound = true;
        }
    }
}
if (!$keysFound) $report .= "None found.\n\n";

// 5. TEXT ALIGNMENT IN CARDS
$report .= "## 5. TEXT ALIGNMENT IN CARDS (welcome.blade.php)\n";
$welcomePath = $viewsPath . DIRECTORY_SEPARATOR . 'welcome.blade.php';
if (file_exists($welcomePath)) {
    $content = file_get_contents($welcomePath);
    // Simple block extraction for .role-card
    preg_match_all('/class="[^"]*role-card[^"]*".*?(?=<div class="[^"]*role-card|<footer|$)/s', $content, $cards);
    if (!empty($cards[0])) {
        foreach ($cards[0] as $idx => $cardHtml) {
            preg_match_all('/<[^>]+text-(align|start|center|end|left|right)[^>]*>|<[^>]+style="[^"]*text-align[^"]*"[^>]*>/', $cardHtml, $aligns, PREG_OFFSET_CAPTURE);
            foreach ($aligns[0] as $align) {
                $report .= "- **Card index " . $idx . " match:** `" . trim(str_replace("\n", " ", htmlspecialchars($align[0]))) . "`\n\n";
            }
        }
    } else {
        $report .= "No .role-card elements found.\n\n";
    }
}

// 6. TABLE HEADER ALIGNMENT & ALL CAPS
$report .= "## 6. TABLE HEADER ALIGNMENT & BROKEN KEYS\n";
$tablesFound = false;
foreach ($files as $file) {
    $lines = file($file);
    $relPath = str_replace($viewsPath . DIRECTORY_SEPARATOR, '', $file);
    
    $inTable = false;
    $thClasses = [];
    $tdClasses = [];
    foreach ($lines as $i => $line) {
        if (strpos($line, '<table') !== false) {
            $inTable = true;
            $thClasses = [];
            $tdClasses = [];
        }
        if ($inTable) {
            if (preg_match('/<th[^>]*class="([^"]+)"/', $line, $m)) {
                $thClasses[] = $m[1];
            } elseif (preg_match('/<th\s*>/', $line)) {
                $thClasses[] = "none";
            }
            if (preg_match('/<td[^>]*class="([^"]+)"/', $line, $m)) {
                $tdClasses[] = $m[1];
            } elseif (preg_match('/<td\s*>/', $line)) {
                $tdClasses[] = "none";
            }
        }
        if (strpos($line, '</table') !== false) {
            $inTable = false;
            // compare
            $report .= "- **File:** " . $relPath . " (Table around line " . ($i+1) . ")\n";
            $report .= "  **TH Classes:** " . implode(', ', array_unique($thClasses)) . "\n";
            $report .= "  **TD Classes:** " . implode(', ', array_unique($tdClasses)) . "\n\n";
            $tablesFound = true;
        }
        
        // ALL CAPS keys
        if (preg_match('/CMS\.[A-Z_\.]+/', $line, $m)) {
            $report .= "- **File:** " . $relPath . " (Line " . ($i+1) . ")\n  **BROKEN KEY (ALL CAPS):** `" . trim($m[0]) . "`\n\n";
            $tablesFound = true;
        }
    }
}
if (!$tablesFound) $report .= "None found.\n\n";

file_put_contents(__DIR__ . '/PATCH_B_DEEP_AUDIT.md', $report);
echo "Audit complete.\n";
