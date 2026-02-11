<?php
/**
 * Dependency installer for cPanel
 * Upload this file to public/ and visit it to install composer dependencies
 */

define('INSTALL_PASSWORD', 'Airport2026InstallDeps');

$password = $_GET['password'] ?? '';
if ($password !== INSTALL_PASSWORD) {
    die('Access denied. Add ?password=Airport2026InstallDeps to URL');
}

set_time_limit(600);
echo "<!DOCTYPE html><html><head><title>Installing Dependencies</title></head><body style='font-family:monospace;background:#000;color:#0f0;padding:20px;'>";
echo "<h2>Installing Laravel Dependencies</h2>";

// Check if composer exists
$composerPath = trim(shell_exec('which composer 2>/dev/null') ?: '');
if (empty($composerPath)) {
    echo "<div style='color:red;'>Composer not found. Trying alternative paths...</div>";
    
    // Try common composer paths
    $possiblePaths = [
        '/usr/local/bin/composer',
        '/usr/bin/composer',
        '~/composer.phar',
        './composer.phar'
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $composerPath = $path;
            echo "<div style='color:yellow;'>Found composer at: $path</div>";
            break;
        }
    }
}

if (empty($composerPath)) {
    echo "<div style='color:red;'>No composer found. Please:</div>";
    echo "<div>1. Upload your vendor/ folder manually, OR</div>";
    echo "<div>2. Contact your hosting provider about composer</div>";
    echo "</body></html>";
    exit;
}

// Go to project root (one level up from public)
$projectRoot = dirname(__DIR__);
chdir($projectRoot);

echo "<div style='color:yellow;'>Running: $composerPath install --no-dev --optimize-autoloader</div>";
echo "<pre>";

// Execute composer install
$output = [];
$returnCode = 0;
exec("$composerPath install --no-dev --optimize-autoloader 2>&1", $output, $returnCode);

foreach ($output as $line) {
    echo htmlspecialchars($line) . "\n";
}

echo "</pre>";

if ($returnCode === 0) {
    echo "<div style='color:green;'><h3>✓ Dependencies installed successfully!</h3></div>";
    echo "<div style='color:yellow;'>Now you can run your migration script.</div>";
    echo "<div style='color:red;'>DELETE THIS FILE: " . __FILE__ . "</div>";
} else {
    echo "<div style='color:red;'>Installation failed. Exit code: $returnCode</div>";
    echo "<div>You may need to upload the vendor folder manually.</div>";
}

echo "</body></html>";
?>