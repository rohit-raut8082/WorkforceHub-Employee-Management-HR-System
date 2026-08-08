<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/leave.php";

requireAdmin();

/* Approve or reject leave */
if (isset($_GET["action"], $_GET["id"])) {
    $id = (int) $_GET["id"];
    $action = $_GET["action"];

    if ($action === "approve") {
        $status = "Approved";
    } elseif ($action === "reject") {
        $status = "Rejected";
    } else {
        $status = "";
    }

    if ($status !== "" && updateLeaveStatus($conn, $id, $status)) {
        $_SESSION["success"] = "Leave request " . strtolower($status) . ".";
    } else {
        $_SESSION["error"] = "Unable to update leave request.";
    }

    header("Location: leaves.php");
    exit();
}

$status_filter = $_GET["status"] ?? "";
$allowed_statuses = ["Pending", "Approved", "Rejected"];

if (!in_array($status_filter, $allowed_statuses)) {
    $status_filter = "";
}

$result = getAllLeaves($conn, $status_filter);

$pageTitle = "Leave Requests";
require_once __DIR__ . "/../includes/header.php";
?>

<div class="panel">
    <div class="panel-title">Leave Requests</div>

    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-sm-4">
            <label class="form-label">Filter by Status</label>
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Pending" <?php echo $status_filter === "Pending" ? "selected" : ""; ?>>Pending</option>
                <option value="Approved" <?php echo $status_filter === "Approved" ? "selected" : ""; ?>>Approved</option>
                <option value="Rejected" <?php echo $status_filter === "Rejected" ? "selected" : ""; ?>>Rejected</option>
            </select>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-ems">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($result->num_rows == 0): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No leave requests found.</td>
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
                            <td>
                                <strong><?php echo htmlspecialchars($leave["name"]); ?></strong><br>
                                <small><?php echo htmlspecialchars($leave["employee_code"]); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($leave["leave_type"]); ?></td>
                            <td><?php echo date("d M Y", strtotime($leave["start_date"])); ?></td>
                            <td><?php echo date("d M Y", strtotime($leave["end_date"])); ?></td>
                            <td><?php echo htmlspecialchars(substr($leave["reason"] ?? "", 0, 50)); ?></td>
                            <td>
                                <span class="badge <?php echo $badge; ?>">
                                    <?php echo htmlspecialchars($leave["status"]); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($leave["status"] === "Pending"): ?>
                                    <a href="?action=approve&id=<?php echo $leave["id"]; ?>"
                                        class="btn btn-sm btn-success"
                                        onclick="return confirm('Approve this leave?')">
                                        Approve
                                    </a>

                                    <a href="?action=reject&id=<?php echo $leave["id"]; ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Reject this leave?')">
                                        Reject
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Reviewed</span>
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