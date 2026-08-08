<?php

// This page redirects users to their dashboard based on their role

// Include authentication file
require_once __DIR__ . '/includes/auth.php';

// Check if user is logged in
if (isLoggedIn()) {
    // User is logged in, redirect based on role
    if (isAdmin()) {
        // Admin users go to admin dashboard
        header("Location: admin/dashboard.php");
    } else {
        // Employee users go to employee profile
        header("Location: employee/profile.php");
    }
    exit();
} else {
    // User not logged in, send to login page
    header("Location: login.php");
    exit();
}
?>

