<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/dashboard.php";
require_once __DIR__ . "/../models/leave.php";

requireAdmin();

$total_employees = getTotalEmployees($conn);
$total_departments = getTotalDepartments($conn);
$present_today = getPresentToday($conn);
$pending_leaves = getPendingLeaves($conn);
$recent_leaves = getRecentLeaves($conn);

$pageTitle = "Dashboard";
require_once __DIR__ . "/../includes/header.php";
?>

<div class="row g-3 mb-2">
<div class="d-flex justify-content-between align-items-start mb-4">

    <!-- Welcome Message -->
    <div>
        <h4 class="fw-bold mb-1">
            <?php
            if ($_SESSION['role'] == 'admin') {
                echo "Welcome back, Admin! 👋";
            } else {
                echo "Welcome back, " . $_SESSION['name'] . "! 👋";
            }
            ?>
        </h4>

        <p class="text-muted mb-0">
            <?php
            if ($_SESSION['role'] == 'admin') {
                echo "Here's what's happening in your organization today.";
            } else {
                echo "Here's what's happening in your employee dashboard today.";
            }
            ?>
        </p>
    </div>


    <!-- Admin / Employee Profile -->
    <div class="dropdown">

        <button
            class="btn bg-white border-0 shadow-sm d-flex align-items-center gap-3 px-3 py-2"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false">

            <!-- Profile Icon -->
            <div
                class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                style="width: 45px; height: 45px;">

                <i class="fas fa-user"></i>

            </div>


            <!-- Name + Role -->
            <div class="text-start">

                <div class="fw-semibold">
                    <?php echo $_SESSION['name']; ?>
                </div>

                <small class="text-muted">
                    <?php echo ucfirst($_SESSION['role']); ?>
                </small>

            </div>


            <!-- Arrow -->
            <i class="fas fa-chevron-down ms-2"></i>

        </button>


        <!-- Dropdown -->
        <ul class="dropdown-menu dropdown-menu-end shadow-sm">

            <li>
                <h6 class="dropdown-header">
                    Account
                </h6>
            </li>

            <li>
                <span class="dropdown-item-text">
                    <strong>
                        <?php echo $_SESSION['name']; ?>
                    </strong>
                    <br>

                    <small class="text-muted">
                        <?php echo ucfirst($_SESSION['role']); ?>
                    </small>
                </span>
            </li>

            <li>
                <hr class="dropdown-divider">
            </li>

            <li>
                <a
                    class="dropdown-item text-danger"
                    href="<?php echo $base; ?>logout.php">

                    <i class="fas fa-sign-out-alt me-2"></i>
                    Logout

                </a>
            </li>

        </ul>

    </div>

</div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card c-blue">
            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="stat-value"><?php echo $total_employees; ?></div>
                <div class="stat-label">Total Employees</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="stat-card c-amber">
            <div class="stat-icon"><i class="fa-solid fa-sitemap"></i></div>
            <div>
                <div class="stat-value"><?php echo $total_departments; ?></div>
                <div class="stat-label">Departments</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="stat-card c-green">
            <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div>
                <div class="stat-value"><?php echo $present_today; ?></div>
                <div class="stat-label">Present Today</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="stat-card c-red">
            <div class="stat-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
            <div>
                <div class="stat-value"><?php echo $pending_leaves; ?></div>
                <div class="stat-label">Pending Leaves</div>
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-title">
        Recent Leave Requests
        <a href="leaves.php" class="btn btn-sm btn-ems-primary">View All</a>
    </div>

    <div class="table-responsive">
        <table class="table table-ems">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Leave Type</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($recent_leaves->num_rows == 0): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No leave requests yet.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($leave = $recent_leaves->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($leave["name"]); ?>
                                <small>(<?php echo htmlspecialchars($leave["employee_code"]); ?>)</small>
                            </td>
                            <td><?php echo htmlspecialchars($leave["leave_type"]); ?></td>
                            <td><?php echo htmlspecialchars($leave["start_date"]); ?></td>
                            <td><?php echo htmlspecialchars($leave["end_date"]); ?></td>
                            <td>
                                <?php if ($leave["status"] === "Approved"): ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php elseif ($leave["status"] === "Rejected"): ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>