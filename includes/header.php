<?php

// Include auth file to check user login status
require_once __DIR__ . '/auth.php';

// Determine the base path for links
// If we're in /admin/ or /employee/ folder, go up one level
$current_file = $_SERVER['SCRIPT_NAME'];
if (strpos($current_file, '/admin/') !== false || strpos($current_file, '/employee/') !== false) {
    $base = '../';  // Go up one level
} else {
    $base = '';     // We're at root level
}

function is_active_page($page_name) {
    $current_page = basename($_SERVER['SCRIPT_NAME']);
    if ($current_page == $page_name) {
        return 'active';
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - WorkforceHub' : 'Employee Management System'; ?></title>
    
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo $base; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php if (isLoggedIn()): ?>
<!-- Main page wrapper for logged-in users -->
<div class="ems-wrapper">
    
    <!-- Sidebar Navigation -->
    <aside class="ems-sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-building-user"></i>
            <span>WorkforceHub</span>
        </div>
        
        <nav class="sidebar-nav">
            <!-- Admin Navigation Menu -->
            <?php if (isAdmin()): ?>
                <p class="sidebar-section">Administration</p>
                <a href="<?php echo $base; ?>admin/dashboard.php" class="<?php echo is_active_page('dashboard.php'); ?>">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a href="<?php echo $base; ?>admin/employees.php" class="<?php echo is_active_page('employees.php'); ?>">
                    <i class="fa-solid fa-users"></i> Employees
                </a>
                <a href="<?php echo $base; ?>admin/departments.php" class="<?php echo is_active_page('departments.php'); ?>">
                    <i class="fa-solid fa-sitemap"></i> Departments
                </a>
                <a href="<?php echo $base; ?>admin/attendance.php" class="<?php echo is_active_page('attendance.php'); ?>">
                    <i class="fa-solid fa-calendar-check"></i> Attendance
                </a>
                <a href="<?php echo $base; ?>admin/leaves.php" class="<?php echo is_active_page('leaves.php'); ?>">
                    <i class="fa-solid fa-envelope-open-text"></i> Leave Requests
                </a>
            
            <!-- Employee Navigation Menu -->
            <?php else: ?>
                <p class="sidebar-section">My Workspace</p>
                <a href="<?php echo $base; ?>employee/profile.php" class="<?php echo is_active_page('profile.php'); ?>">
                    <i class="fa-solid fa-id-card"></i> My Profile
                </a>
                <a href="<?php echo $base; ?>employee/attendance.php" class="<?php echo is_active_page('attendance.php'); ?>">
                    <i class="fa-solid fa-calendar-check"></i> My Attendance
                </a>
                <a href="<?php echo $base; ?>employee/leave.php" class="<?php echo is_active_page('leave.php'); ?>">
                    <i class="fa-solid fa-envelope-open-text"></i> My Leaves
                </a>
            <?php endif; ?>
            
            <!-- Logout Link -->
            <a href="<?php echo $base; ?>logout.php" class="logout-link">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="ems-main">
        <main class="ems-content">
            <?php require_once __DIR__ . "/alert.php"; ?>
<?php else: ?>
    <!-- Auth/Login page (not logged in) -->
<?php endif; ?>
