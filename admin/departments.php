<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/department.php";

requireAdmin();

/* Add department */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_department"])) {
    $name = trim($_POST["department_name"]);

    if ($name === "") {
        $_SESSION["error"] = "Department name is required.";
    } elseif (storeDepartment($conn, $name)) {
        $_SESSION["success"] = "Department added successfully.";
    } else {
        $_SESSION["error"] = "Unable to add department.";
    }

    header("Location: departments.php");
    exit();
}

/* Update department */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_department"])) {
    $id = (int) $_POST["id"];
    $name = trim($_POST["department_name"]);

    if ($name === "") {
        $_SESSION["error"] = "Department name is required.";
    } elseif (updateDepartment($conn, $id, $name)) {
        $_SESSION["success"] = "Department updated successfully.";
    } else {
        $_SESSION["error"] = "Unable to update department.";
    }

    header("Location: departments.php");
    exit();
}

/* Delete department */
if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"];

    if (deleteDepartment($conn, $id)) {
        $_SESSION["success"] = "Department deleted successfully.";
    } else {
        $_SESSION["error"] = "Unable to delete department.";
    }

    header("Location: departments.php");
    exit();
}

$departments = getAllDepartments($conn);

$pageTitle = "Departments";
require_once __DIR__ . "/../includes/header.php";
?>

<div class="panel">
    <div class="panel-title">Add New Department</div>

    <form method="POST" class="row g-2 align-items-end">
        <div class="col-sm-8">
            <label class="form-label">Department Name</label>
            <input type="text" name="department_name" class="form-control" required>
        </div>

        <div class="col-sm-4">
            <button type="submit" name="add_department" class="btn btn-ems-primary w-100">
                Add Department
            </button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-title">All Departments</div>

    <div class="table-responsive">
        <table class="table table-ems">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Department</th>
                    <th>Employees</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($departments->num_rows == 0): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">No departments found.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($department = $departments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $department["id"]; ?></td>
                            <td><?php echo htmlspecialchars($department["department_name"]); ?></td>
                            <td><?php echo $department["employee_count"]; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal<?php echo $department["id"]; ?>">
                                    Edit
                                </button>

                                <a href="?delete=<?php echo $department["id"]; ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this department?')">
                                    Delete
                                </a>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?php echo $department["id"]; ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Department</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?php echo $department["id"]; ?>">

                                            <label class="form-label">Department Name</label>
                                            <input type="text"
                                                name="department_name"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($department["department_name"]); ?>"
                                                required>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" name="update_department" class="btn btn-primary">
                                                Update
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>