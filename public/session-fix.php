<?php
/**
 * SESSION FIX GENERATOR
 * 
 * Generates the correct .env settings for your domain
 */

define('SESSION_FIX_PASSWORD', 'Airport2026SessionFix');

$password = $_GET['password'] ?? '';
if ($password !== SESSION_FIX_PASSWORD) {
    die('Access denied. Add ?password=Airport2026SessionFix to URL');
}

echo "<!DOCTYPE html><html><head><title>Session Configuration Fix</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}.fix{color:#00ffff;}</style></head><body>";

echo "<h2>Session Configuration Fix</h2>";

$currentDomain = $_SERVER['HTTP_HOST'] ?? 'unknown';
$isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

echo "<div class='info'>Current domain detected: {$currentDomain}</div>";
echo "<div class='info'>HTTPS detected: " . ($isHttps ? 'Yes' : 'No') . "</div><br>";

echo "<div class='fix'><h3>🔧 Add these lines to your .env file:</h3></div>";
echo "<textarea style='width:100%;height:300px;background:#333;color:#0f0;border:1px solid #555;font-family:monospace;'>";
echo "# Session Configuration for Production\n";
echo "SESSION_DRIVER=database\n";
echo "SESSION_LIFETIME=1200\n";
echo "SESSION_ENCRYPT=false\n";
echo "SESSION_PATH=/\n";
echo "SESSION_DOMAIN={$currentDomain}\n";
if ($isHttps) {
    echo "SESSION_SECURE=true\n";
} else {
    echo "SESSION_SECURE=false\n";
}
echo "SESSION_SAME_SITE=lax\n\n";

echo "# Performance Settings\n";
echo "CACHE_STORE=database\n";
echo "QUEUE_CONNECTION=database\n\n";

echo "# Security Settings\n";
echo "APP_ENV=production\n";
echo "APP_DEBUG=false\n";
echo "LOG_LEVEL=error\n";
echo "</textarea><br>";

echo "<div class='fix'><h3>📝 Quick Steps:</h3></div>";
echo "<div class='success'>1. Copy the settings above</div>";
echo "<div class='success'>2. Add them to your .env file on cPanel</div>";
echo "<div class='success'>3. Run the optimizer: https://{$currentDomain}/optimize.php?password=Airport2026Optimize</div>";
echo "<div class='success'>4. Test job acceptance again</div><br>";

echo "<div class='info'>💡 If still getting 403 errors, the issue is likely:</div>";
echo "<div class='error'>• Driver not properly logged in</div>";
echo "<div class='error'>• Job not assigned to the current driver</div>";
echo "<div class='error'>• Session expired</div>";
echo "<div class='error'>• Database connection issues</div><br>";

echo "<div class='fix'><h3>🧪 Test Commands:</h3></div>";
echo "<div class='info'>Diagnose: https://{$currentDomain}/diagnose.php?password=Airport2026Diagnose</div>";
echo "<div class='info'>CSRF Test: https://{$currentDomain}/csrf-test.php?password=Airport2026CSRFTest</div><br>";

echo "<div class='error'><h3>⚠ DELETE THIS FILE AFTER USE!</h3></div>";

echo "</body></html>";
?>