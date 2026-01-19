<?php
/**
 * Laravel Server Requirements Checker
 * 
 * Upload this file to your cPanel public folder and visit it in browser
 * to check if your server meets Laravel's requirements.
 * 
 * DELETE THIS FILE after checking!
 */

?>
<!DOCTYPE html>
<html>
<head>
    <title>Server Requirements Check - Laravel</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        .requirement { padding: 12px; margin: 8px 0; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; }
        .pass { background: #d4edda; border-left: 4px solid #28a745; }
        .fail { background: #f8d7da; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; }
        .status { font-weight: bold; padding: 4px 12px; border-radius: 4px; }
        .status.pass { background: #28a745; color: white; }
        .status.fail { background: #dc3545; color: white; }
        .status.warning { background: #ffc107; color: #333; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 4px; margin: 20px 0; border-left: 4px solid #2196F3; }
        .value { font-family: monospace; background: #f0f0f0; padding: 2px 8px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Laravel Server Requirements Check</h1>
        <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_NAME']; ?></p>
        <p><strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>

        <h2>PHP Requirements</h2>

        <?php
        $requirements = [
            'PHP Version >= 8.1' => version_compare(PHP_VERSION, '8.1.0', '>='),
            'BCMath Extension' => extension_loaded('bcmath'),
            'Ctype Extension' => extension_loaded('ctype'),
            'cURL Extension' => extension_loaded('curl'),
            'DOM Extension' => extension_loaded('dom'),
            'Fileinfo Extension' => extension_loaded('fileinfo'),
            'JSON Extension' => extension_loaded('json'),
            'Mbstring Extension' => extension_loaded('mbstring'),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'PCRE Extension' => extension_loaded('pcre'),
            'PDO Extension' => extension_loaded('pdo'),
            'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
            'Tokenizer Extension' => extension_loaded('tokenizer'),
            'XML Extension' => extension_loaded('xml'),
        ];

        $allPassed = true;
        foreach ($requirements as $requirement => $status) {
            $class = $status ? 'pass' : 'fail';
            $statusText = $status ? 'PASS' : 'FAIL';
            if (!$status) $allPassed = false;
            
            echo "<div class='requirement $class'>";
            echo "<span>$requirement</span>";
            echo "<span class='status $class'>$statusText</span>";
            echo "</div>";
        }
        ?>

        <h2>PHP Configuration</h2>

        <?php
        $configs = [
            'PHP Version' => PHP_VERSION,
            'Max Execution Time' => ini_get('max_execution_time') . ' seconds',
            'Memory Limit' => ini_get('memory_limit'),
            'Upload Max Filesize' => ini_get('upload_max_filesize'),
            'Post Max Size' => ini_get('post_max_size'),
            'Display Errors' => ini_get('display_errors') ? 'On (Turn OFF for production)' : 'Off',
            'Error Reporting' => ini_get('error_reporting'),
        ];

        foreach ($configs as $config => $value) {
            $class = 'pass';
            if ($config === 'Display Errors' && ini_get('display_errors')) {
                $class = 'warning';
            }
            echo "<div class='requirement $class'>";
            echo "<span>$config</span>";
            echo "<span class='value'>$value</span>";
            echo "</div>";
        }
        ?>

        <h2>Recommended Settings</h2>

        <?php
        $recommendations = [
            'Memory Limit >= 128M' => [
                'status' => (int)ini_get('memory_limit') >= 128 || ini_get('memory_limit') === '-1',
                'current' => ini_get('memory_limit'),
                'recommended' => '256M or higher'
            ],
            'Max Execution Time >= 60' => [
                'status' => (int)ini_get('max_execution_time') >= 60 || (int)ini_get('max_execution_time') === 0,
                'current' => ini_get('max_execution_time') . ' seconds',
                'recommended' => '300 seconds'
            ],
        ];

        foreach ($recommendations as $name => $check) {
            $class = $check['status'] ? 'pass' : 'warning';
            $statusText = $check['status'] ? 'OK' : 'WARNING';
            
            echo "<div class='requirement $class'>";
            echo "<span>$name<br><small>Current: {$check['current']} | Recommended: {$check['recommended']}</small></span>";
            echo "<span class='status $class'>$statusText</span>";
            echo "</div>";
        }
        ?>

        <h2>File Permissions Check</h2>

        <?php
        // Check if we can write to key directories
        $writableChecks = [
            'Storage Directory Writable' => is_writable(__DIR__ . '/../storage'),
            'Bootstrap Cache Writable' => is_writable(__DIR__ . '/../bootstrap/cache'),
        ];

        foreach ($writableChecks as $check => $status) {
            $class = $status ? 'pass' : 'fail';
            $statusText = $status ? 'WRITABLE' : 'NOT WRITABLE';
            if (!$status) $allPassed = false;
            
            echo "<div class='requirement $class'>";
            echo "<span>$check</span>";
            echo "<span class='status $class'>$statusText</span>";
            echo "</div>";
        }
        ?>

        <h2>Additional Checks</h2>

        <?php
        $additionalChecks = [
            '.env File Exists' => file_exists(__DIR__ . '/../.env'),
            'Vendor Directory Exists' => is_dir(__DIR__ . '/../vendor'),
            '.htaccess Exists' => file_exists(__DIR__ . '/.htaccess'),
        ];

        foreach ($additionalChecks as $check => $status) {
            $class = $status ? 'pass' : 'fail';
            $statusText = $status ? 'YES' : 'NO';
            if (!$status) $allPassed = false;
            
            echo "<div class='requirement $class'>";
            echo "<span>$check</span>";
            echo "<span class='status $class'>$statusText</span>";
            echo "</div>";
        }
        ?>

        <div class="info">
            <?php if ($allPassed): ?>
                <h3 style="color: #28a745; margin-top: 0;">✅ All Requirements Met!</h3>
                <p>Your server meets all Laravel requirements. You can proceed with deployment.</p>
            <?php else: ?>
                <h3 style="color: #dc3545; margin-top: 0;">❌ Some Requirements Not Met</h3>
                <p>Please fix the failed requirements before deploying Laravel. Contact your hosting provider if you need help enabling PHP extensions.</p>
            <?php endif; ?>
            
            <p><strong>⚠️ IMPORTANT:</strong> Delete this file (<?php echo basename(__FILE__); ?>) after checking!</p>
        </div>

        <h2>Next Steps</h2>
        <ol>
            <li>Ensure all requirements marked as "FAIL" are resolved</li>
            <li>Configure your domain to point to the <code>/public</code> folder</li>
            <li>Update your <code>.env</code> file with production settings</li>
            <li>Run database migrations</li>
            <li>Clear all caches</li>
            <li>Install SSL certificate</li>
            <li><strong>Delete this requirements check file!</strong></li>
        </ol>
    </div>
</body>
</html>
