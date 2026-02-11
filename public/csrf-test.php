<?php
/**
 * CSRF TOKEN TESTER
 * 
 * Tests CSRF token generation and validation
 */

define('CSRF_TEST_PASSWORD', 'Airport2026CSRFTest');

$password = $_GET['password'] ?? '';
if ($password !== CSRF_TEST_PASSWORD) {
    die('Access denied. Add ?password=Airport2026CSRFTest to URL');
}

echo "<!DOCTYPE html><html><head><title>CSRF Test</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}</style></head><body>";

echo "<h2>CSRF Token Test</h2>";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Start session if not already started
    if (!session()->isStarted()) {
        session()->start();
    }
    
    $csrfToken = csrf_token();
    
    echo "<div class='info'>Current CSRF Token:</div>";
    echo "<div class='success'>{$csrfToken}</div><br>";
    
    echo "<div class='info'>Session ID:</div>";
    echo "<div class='success'>" . session()->getId() . "</div><br>";
    
    echo "<div class='info'>Test AJAX call with this token:</div>";
    echo "<script>";
    echo "console.log('CSRF Token:', '{$csrfToken}');";
    echo "console.log('Session ID:', '" . session()->getId() . "');";
    echo "</script>";
    
    echo "<div class='info'>Copy this CSRF token to test manually in browser console:</div>";
    echo "<textarea style='width:100%;height:100px;background:#333;color:#0f0;border:1px solid #555;'>{$csrfToken}</textarea><br>";
    
    echo "<br><div class='info'>🧪 Manual Test Instructions:</div>";
    echo "<div class='success'>1. Open browser console (F12)</div>";
    echo "<div class='success'>2. Run this test fetch command:</div>";
    echo "<textarea style='width:100%;height:200px;background:#333;color:#0f0;border:1px solid #555;'>";
    echo "fetch('{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}/driver/jobs/1/accept', {\n";
    echo "  method: 'POST',\n";
    echo "  headers: {\n";
    echo "    'Content-Type': 'application/json',\n";
    echo "    'X-CSRF-TOKEN': '{$csrfToken}',\n";
    echo "    'Accept': 'application/json'\n";
    echo "  }\n";
    echo "}).then(r => console.log('Status:', r.status)).catch(e => console.error(e));";
    echo "</textarea>";
    
    echo "<br><br><div class='error'>⚠ DELETE THIS FILE AFTER TESTING!</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body></html>";
?>