<?php

// Test script to create a sample notification
require_once 'bootstrap/app.php';

use App\Models\UserNotification;

// Create a test notification for admin users
UserNotification::createForAdmins(
    'Test Driver Response',
    'This is a test notification to verify the admin notification system is working correctly.'
);

echo "Test notification created successfully!\n";