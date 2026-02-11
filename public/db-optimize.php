<?php
/**
 * DATABASE OPTIMIZER
 * 
 * Optimizes database tables and adds indexes for better performance
 */

define('DB_OPTIMIZE_PASSWORD', 'Airport2026DBOptimize');

$password = $_GET['password'] ?? '';
if ($password !== DB_OPTIMIZE_PASSWORD) {
    die('Access denied. Add ?password=Airport2026DBOptimize to URL');
}

echo "<!DOCTYPE html><html><head><title>Database Optimizer</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}</style></head><body>";

echo "<h2>Database Performance Optimizer</h2>";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    $db = \Illuminate\Support\Facades\DB::connection()->getPdo();
    
    echo "<div class='info'>Optimizing database tables...</div><br>";
    
    // Get all tables - use a more reliable method
    $tablesResult = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
    
    foreach ($tablesResult as $tableObj) {
        // Convert object to array to get first value (table name)
        $tableArray = (array) $tableObj;
        $tableName = array_values($tableArray)[0];
        
        try {
            // Optimize each table
            \Illuminate\Support\Facades\DB::statement("OPTIMIZE TABLE `{$tableName}`");
            echo "<div class='success'>✓ Optimized table: {$tableName}</div>";
        } catch (Exception $e) {
            echo "<div class='error'>✗ Failed to optimize {$tableName}: " . $e->getMessage() . "</div>";
        }
    }
    
    echo "<br><div class='info'>Adding performance indexes...</div><br>";
    
    // Add common performance indexes
    try {
        \Illuminate\Support\Facades\DB::statement("CREATE INDEX idx_users_email ON users(email)");
        echo "<div class='success'>✓ Added index on users.email</div>";
    } catch (Exception $e) {
        echo "<div class='info'>ℹ users.email index already exists</div>";
    }
    
    try {
        \Illuminate\Support\Facades\DB::statement("CREATE INDEX idx_sessions_last_activity ON sessions(last_activity)");
        echo "<div class='success'>✓ Added index on sessions.last_activity</div>";
    } catch (Exception $e) {
        echo "<div class='info'>ℹ sessions.last_activity index already exists</div>";
    }
    
    echo "<br><div class='success'><h3>✓ DATABASE OPTIMIZATION COMPLETED!</h3></div>";
    echo "<div class='error'><h3>⚠ DELETE THIS FILE NOW!</h3></div>";
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body></html>";
?>