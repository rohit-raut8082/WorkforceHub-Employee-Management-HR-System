<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/attendance.php";

requireAdmin();

/* Save attendance */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["mark_attendance"])) {
    $date = $_POST["attendance_date"];
    $status_list = $_POST["status"] ?? [];

    foreach ($status_list as $employee_id => $status) {
        $employee_id = (int) $employee_id;

        if ($status === "Present" || $status === "Absent") {
            saveAttendance($conn, $employee_id, $date, $status);
        }
    }

    $_SESSION["success"] = "Attendance saved successfully.";
header("Location: attendance.php?date=" . urlencode($date));
    exit();
}

$selected_date = $_GET["date"] ?? date("Y-m-d");
$report_month = $_GET["month"] ?? date("Y-m");

$today_result = getAttendanceByDate($conn, $selected_date);

$year = (int) date("Y", strtotime($report_month . "-01"));
$month = (int) date("m", strtotime($report_month . "-01"));

$report_result = getMonthlyAttendance($conn, $year, $month);

$pageTitle = "Attendance";
require_once __DIR__ . "/../includes/header.php";
?>

<div class="panel">
    <div class="panel-title">Mark Attendance</div>

    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-sm-4">
            <label class="form-label">Select Date</label>
            <input type="date"
                   name="date"
                   class="form-control"
                   value="<?php echo htmlspecialchars($selected_date); ?>"
                   onchange="this.form.submit()">
        </div>
    </form>

    <form method="POST">
        <input type="hidden" name="attendance_date" value="<?php echo htmlspecialchars($selected_date); ?>">

        <div class="table-responsive">
            <table class="table table-ems">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($today_result->num_rows == 0): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">No employees found.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($employee = $today_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($employee["employee_code"]); ?></td>
                            <td><?php echo htmlspecialchars($employee["name"]); ?></td>
                            <td><?php echo htmlspecialchars($employee["department_name"] ?: "Unassigned"); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <input type="radio"
                                           class="btn-check"
                                           name="status[<?php echo $employee["id"]; ?>]"
                                           id="present<?php echo $employee["id"]; ?>"
                                           value="Present"
                                           <?php echo $employee["status"] === "Present" ? "checked" : ""; ?>>

                                    <label class="btn btn-outline-success" for="present<?php echo $employee["id"]; ?>">
                                        Present
                                    </label>

                                    <input type="radio"
                                           class="btn-check"
                                           name="status[<?php echo $employee["id"]; ?>]"
                                           id="absent<?php echo $employee["id"]; ?>"
                                           value="Absent"
                                           <?php echo $employee["status"] === "Absent" ? "checked" : ""; ?>>

                                    <label class="btn btn-outline-danger" for="absent<?php echo $employee["id"]; ?>">
                                        Absent
                                    </label>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button type="submit" name="mark_attendance" class="btn btn-ems-primary">
            Save Attendance
        </button>
    </form>
</div>

<div class="panel">
    <div class="panel-title">Monthly Attendance Report</div>

    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-sm-4">
            <label class="form-label">Select Month</label>
            <input type="month"
                   name="month"
                   class="form-control"
                   value="<?php echo htmlspecialchars($report_month); ?>"
                   onchange="this.form.submit()">
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-ems">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th class="text-center">Present Days</th>
                    <th class="text-center">Absent Days</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($report_result->num_rows == 0): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted">No records.</td>
                </tr>
            <?php else: ?>
                <?php while ($row = $report_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["employee_code"]); ?></td>
                        <td><?php echo htmlspecialchars($row["name"]); ?></td>
                        <td class="text-center">
                            <span class="badge bg-success"><?php echo $row["present_days"]; ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-danger"><?php echo $row["absent_days"]; ?></span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
