<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/employee.php";

requireEmployee();

$employee_id = $_SESSION["employee_id"];
$uploadDir = __DIR__ . "/../uploads/";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_profile"])) {
    $phone = trim($_POST["phone"]);
    $gender = trim($_POST["gender"]);
    $photo = $_POST["old_photo"] ?? "";

    if (!empty($_FILES["photo"]["name"])) {
        $extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "webp"];

        if (!in_array($extension, $allowed) || $_FILES["photo"]["size"] > 2 * 1024 * 1024) {
            $_SESSION["error"] = "Photo must be JPG, PNG or WEBP and under 2MB.";
            header("Location: profile.php");
            exit();
        }

        $photo = "emp_" . time() . "_" . uniqid() . "." . $extension;

        if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $uploadDir . $photo)) {
            $_SESSION["error"] = "Unable to upload photo.";
            header("Location: profile.php");
            exit();
        }
    }

    $data = [
        "id" => $employee_id,
        "name" => $_SESSION["name"],
        "email" => $_SESSION["email"],
        "phone" => $phone,
        "gender" => $gender,
        "department_id" => null,
        "salary" => 0,
        "joining_date" => null,
        "photo" => $photo
    ];

    /* Only phone, gender and photo are updated by the employee. */
    $sql = "UPDATE employees SET phone=?, gender=?, photo=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $phone, $gender, $photo, $employee_id);

    if ($stmt->execute()) {
        $_SESSION["success"] = "Profile updated successfully.";
    } else {
        $_SESSION["error"] = "Unable to update profile.";
    }

    header("Location: profile.php");
    exit();
}

$result = getEmployeeById($conn, $employee_id);

if ($result->num_rows == 0) {
    $_SESSION["error"] = "Employee record not found.";
    header("Location: ../logout.php");
    exit();
}

$employee = $result->fetch_assoc();

$pageTitle = "My Profile";
require_once __DIR__ . "/../includes/header.php";
?>

<div class="row g-3">
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
    <div class="col-lg-4">
        <div class="panel text-center">
            <?php if (!empty($employee["photo"])): ?>
                <img src="../uploads/<?php echo htmlspecialchars($employee["photo"]); ?>"
                    class="rounded-circle mb-3"
                    style="width:110px;height:110px;object-fit:cover;">
            <?php else: ?>
                <div class="rounded-circle mb-3 d-flex align-items-center justify-content-center bg-light"
                    style="width:110px;height:110px;margin:auto;">
                    <i class="fa-solid fa-user fa-3x text-muted"></i>
                </div>
            <?php endif; ?>

            <h5><?php echo htmlspecialchars($employee["name"]); ?></h5>
            <p class="text-muted"><?php echo htmlspecialchars($employee["employee_code"]); ?></p>
            <span class="badge bg-secondary">
                <?php echo htmlspecialchars($employee["department_name"] ?? "Unassigned"); ?>
            </span>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="panel">
            <div class="panel-title">Employee Information</div>

            <div class="row g-3">
                <div class="col-sm-6">
                    <strong>Email:</strong><br>
                    <span class="text-muted"><?php echo htmlspecialchars($employee["email"]); ?></span>
                </div>

                <div class="col-sm-6">
                    <strong>Phone:</strong><br>
                    <span class="text-muted"><?php echo htmlspecialchars($employee["phone"] ?: "-"); ?></span>
                </div>

                <div class="col-sm-6">
                    <strong>Gender:</strong><br>
                    <span class="text-muted"><?php echo htmlspecialchars($employee["gender"] ?: "-"); ?></span>
                </div>

                <div class="col-sm-6">
                    <strong>Joining Date:</strong><br>
                    <span class="text-muted">
                        <?php echo !empty($employee["joining_date"]) ? date("d M Y", strtotime($employee["joining_date"])) : "-"; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-title">Update Profile</div>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="old_photo" value="<?php echo htmlspecialchars($employee["photo"] ?? ""); ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text"
                            name="phone"
                            class="form-control"
                            value="<?php echo htmlspecialchars($employee["phone"] ?? ""); ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select</option>
                            <option value="Male" <?php echo $employee["gender"] === "Male" ? "selected" : ""; ?>>Male</option>
                            <option value="Female" <?php echo $employee["gender"] === "Female" ? "selected" : ""; ?>>Female</option>
                            <option value="Other" <?php echo $employee["gender"] === "Other" ? "selected" : ""; ?>>Other</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Profile Photo</label>
                        <input type="file"
                            name="photo"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,.webp">
                    </div>
                </div>

                <button type="submit" name="update_profile" class="btn btn-ems-primary mt-3">
                    Save Changes
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>