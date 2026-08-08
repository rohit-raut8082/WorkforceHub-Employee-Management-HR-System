<?php

require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/employee.php";
require_once __DIR__ . "/../models/department.php";
require_once __DIR__ . "/../models/user.php";

requireAdmin();

$uploadDir = __DIR__ . "/../uploads/";

/* Delete employee */
if (isset($_GET["delete"])) {

    $id = $_GET["delete"];

    $photoResult = getEmployeePhoto($conn, $id);

    if ($photoResult->num_rows > 0) {
        $photoRow = $photoResult->fetch_assoc();

        if (!empty($photoRow["photo"]) && file_exists($uploadDir . $photoRow["photo"])) {
            unlink($uploadDir . $photoRow["photo"]);
        }
    }

    if (deleteEmployee($conn, $id)) {
        $_SESSION["success"] = "Employee deleted successfully.";
    } else {
        $_SESSION["error"] = "Unable to delete employee.";
    }

    header("Location: employees.php");
    exit();
}

/* Add employee */
if (isset($_POST["add_employee"])) {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST['password']);
    $phone = trim($_POST["phone"]);
    $gender = trim($_POST["gender"]);
    $department_id = $_POST["department_id"];
    $salary = $_POST["salary"];
    $joining_date = $_POST["joining_date"];

    $errors = [];

    if ($name == "") {
        $errors[] = "Employee name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }

    if (emailExists($conn, $email)) {
        $errors[] = "Email already exists.";
    }

    if (empty($password)) {
        $errors[] = "Initial password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Initial password must be at least 6 characters.";
    }

    if ($phone != "" && !preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone number must contain 10 digits.";
    }

    if ($gender != "" && $gender != "Male" && $gender != "Female" && $gender != "Other") {
        $errors[] = "Invalid gender.";
    }

    if ($salary != "" && !is_numeric($salary)) {
        $errors[] = "Salary must be a number.";
    }

    $photo = "";

    if (!empty($_FILES["photo"]["name"])) {

        $extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));

        if ($extension != "jpg" && $extension != "jpeg" && $extension != "png" && $extension != "webp") {
            $errors[] = "Photo must be JPG, JPEG, PNG or WEBP.";
        }

        if ($_FILES["photo"]["size"] > 2 * 1024 * 1024) {
            $errors[] = "Photo must be less than 2MB.";
        }

        if (empty($errors)) {
            $photo = "emp_" . time() . "." . $extension;
        }
    }

    if (empty($errors)) {

        $code = getNextEmployeeCode($conn);

        $data = [
            "employee_code" => $code,
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            "gender" => $gender,
            "department_id" => $department_id == "" ? null : $department_id,
            "salary" => $salary == "" ? 0 : $salary,
            "joining_date" => $joining_date,
            "photo" => $photo
        ];

        if (storeEmployee($conn, $data)) {

            $employee_id = $conn->insert_id;

            if ($photo != "") {
                move_uploaded_file(
                    $_FILES["photo"]["tmp_name"],
                    $uploadDir . $photo
                );
            }

            createUser(
                $conn,
                $name,
                $email,
                $password,
                "employee",
                $employee_id
            );

            $_SESSION["success"] = "Employee added successfully. Employee Code: " . $code;
        } else {
            $_SESSION["error"] = "Unable to add employee.";
        }
    } else {
        $_SESSION["error"] = implode(" ", $errors);
    }

    header("Location: employees.php");
    exit();
}

$employees = getAllEmployees($conn);
$departments = getAllDepartments($conn);

$pageTitle = "Employees";

require_once __DIR__ . "/../includes/header.php";
?>

<div class="panel">
    <div class="panel-title">Add New Employee</div>

    <form method="POST" enctype="multipart/form-data">

        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label">Employee Name *</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Initial Password *</label>
                <input type="password"
                    name="password"
                    class="form-control"
                    minlength="6"
                    required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Gender</label>

                <select name="gender" class="form-select">
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Department</label>

                <select name="department_id" class="form-select">
                    <option value="">Unassigned</option>

                    <?php while ($department = $departments->fetch_assoc()) { ?>

                        <option value="<?php echo $department["id"]; ?>">
                            <?php echo htmlspecialchars($department["department_name"]); ?>
                        </option>

                    <?php } ?>

                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Salary</label>
                <input type="number" name="salary" class="form-control" min="0">
            </div>

            <div class="col-md-6">
                <label class="form-label">Joining Date</label>
                <input type="date"
                    name="joining_date"
                    class="form-control"
                    value="<?php echo date("Y-m-d"); ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Photo</label>
                <input type="file"
                    name="photo"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,.webp">
            </div>

        </div>

        <button type="submit"
            name="add_employee"
            class="btn btn-ems-primary mt-3">
            Add Employee
        </button>

    </form>
</div>

<div class="panel">

    <div class="panel-title">All Employees</div>

    <div class="table-responsive">

        <table class="table table-ems">

            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Department</th>
                    <th>Salary</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($employees->num_rows == 0) { ?>

                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No employees found.
                        </td>
                    </tr>

                <?php } else { ?>

                    <?php while ($employee = $employees->fetch_assoc()) { ?>

                        <tr>

                            <td><?php echo htmlspecialchars($employee["employee_code"]); ?></td>

                            <td><?php echo htmlspecialchars($employee["name"]); ?></td>

                            <td><?php echo htmlspecialchars($employee["email"]); ?></td>

                            <td><?php echo htmlspecialchars($employee["phone"] ?: "-"); ?></td>

                            <td>
                                <?php
                                if ($employee["department_name"] == "") {
                                    echo "Unassigned";
                                } else {
                                    echo htmlspecialchars($employee["department_name"]);
                                }
                                ?>
                            </td>

                            <td><?php echo htmlspecialchars($employee["salary"]); ?></td>

                            <td>
                                <a href="?delete=<?php echo $employee["id"]; ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this employee?')">
                                    Delete
                                </a>
                            </td>

                        </tr>

                    <?php } ?>

                <?php } ?>

            </tbody>

        </table>

    </div>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>