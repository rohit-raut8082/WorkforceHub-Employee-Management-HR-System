<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/attendance.php";

requireEmployee();

$employee_id = $_SESSION["employee_id"];
$month = $_GET["month"] ?? date("Y-m");

$result = getEmployeeAttendance($conn, $employee_id, $month);

$present_count = 0;
$absent_count = 0;
$records = [];

while ($row = $result->fetch_assoc()) {
    $records[] = $row;

    if ($row["status"] === "Present") {
        $present_count++;
    } else {
        $absent_count++;
    }
}

$pageTitle = "My Attendance";
require_once __DIR__ . "/../includes/header.php";
?>

<div class="row g-3 mb-3">
    <div class="col-sm-6">
        <div class="stat-card c-green">
            <div class="stat-icon"><i class="fa-solid fa-check"></i></div>
            <div>
                <div class="stat-value"><?php echo $present_count; ?></div>
                <div class="stat-label">Present Days</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="stat-card c-red">
            <div class="stat-icon"><i class="fa-solid fa-xmark"></i></div>
            <div>
                <div class="stat-value"><?php echo $absent_count; ?></div>
                <div class="stat-label">Absent Days</div>
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-title">Attendance History</div>

    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-sm-4">
            <label class="form-label">Select Month</label>
            <input type="month"
                name="month"
                class="form-control"
                value="<?php echo htmlspecialchars($month); ?>"
                onchange="this.form.submit()">
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-ems">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($records) == 0): ?>
                    <tr>
                        <td colspan="2" class="text-center text-muted">
                            No attendance records for this month.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?php echo date("d M Y (l)", strtotime($record["attendance_date"])); ?></td>
                            <td>
                                <span class="badge <?php echo $record["status"] === "Present" ? "bg-success" : "bg-danger"; ?>">
                                    <?php echo htmlspecialchars($record["status"]); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>