<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/leave.php";

requireEmployee();

$employee_id = $_SESSION["employee_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["apply_leave"])) {
    $leave_type = trim($_POST["leave_type"]);
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $reason = trim($_POST["reason"]);

    if ($leave_type === "" || $start_date === "" || $end_date === "") {
        $_SESSION["error"] = "Please fill in all required fields.";
} elseif (strtotime($end_date) < strtotime($start_date)) {
        $_SESSION["error"] = "End date cannot be before start date.";
} elseif (applyLeave($conn, $employee_id, $leave_type, $start_date, $end_date, $reason)) {
        $_SESSION["success"] = "Leave request submitted successfully.";
} else {
        $_SESSION["error"] = "Unable to submit leave request.";
}

    header("Location: leave.php");
    exit();
}

$result = getEmployeeLeaves($conn, $employee_id);

$pageTitle = "My Leaves";
require_once __DIR__ . "/../includes/header.php";
?>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="panel">
            <div class="panel-title">Apply for Leave</div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Leave Type *</label>
                    <select name="leave_type" class="form-select" required>
                        <option value="">Select leave type</option>
                        <option>Sick Leave</option>
                        <option>Casual Leave</option>
                        <option>Earned Leave</option>
                        <option>Maternity/Paternity Leave</option>
                        <option>Unpaid Leave</option>
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label">Start Date *</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label">End Date *</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Reason</label>
                    <textarea name="reason" class="form-control" rows="4"></textarea>
                </div>

                <button type="submit" name="apply_leave" class="btn btn-ems-primary w-100">
                    Submit Request
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="panel">
            <div class="panel-title">Leave History</div>

            <div class="table-responsive">
                <table class="table table-ems">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if ($result->num_rows == 0): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No leave requests yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php while ($leave = $result->fetch_assoc()): ?>
                            <?php
                            if ($leave["status"] === "Approved") {
                                $badge = "bg-success";
                            } elseif ($leave["status"] === "Rejected") {
                                $badge = "bg-danger";
                            } else {
                                $badge = "bg-warning";
                            }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($leave["leave_type"]); ?></td>
                                <td><?php echo date("d M Y", strtotime($leave["start_date"])); ?></td>
                                <td><?php echo date("d M Y", strtotime($leave["end_date"])); ?></td>
                                <td>
                                    <span class="badge <?php echo $badge; ?>">
                                        <?php echo htmlspecialchars($leave["status"]); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
