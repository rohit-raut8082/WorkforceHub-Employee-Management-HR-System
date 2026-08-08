<?php
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/models/employee.php";
require_once __DIR__ . "/models/user.php";

if (isLoggedIn()) {
    header("Location: " . (isAdmin() ? "admin/dashboard.php" : "employee/profile.php"));
    exit();
}

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $employee_code = trim($_POST["employee_code"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($employee_code) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $result = getEmployeeByCodeAndEmail($conn, $employee_code, $email);

        if ($result->num_rows == 0) {
            $errors[] = "Employee code and email do not match.";
        } elseif (emailExists($conn, $email)) {
            $errors[] = "An account already exists for this email.";
        } else {
            $employee = $result->fetch_assoc();

            if (createUser(
                $conn,
                $employee["name"],
                $email,
                $password,
                "employee",
                $employee["id"]
            )) {
                $success = true;
            } else {
                $errors[] = "Unable to create account.";
            }
        }
    }
}

$pageTitle = "Register";
require_once __DIR__ . "/includes/header.php";
?>

<div class="auth-shell">
  <div class="auth-card">
    <div class="auth-brand"><i class="fa-solid fa-building-user"></i>WorkforceHub</div>
    <p class="auth-sub">Activate your employee account</p>

    <?php if ($success): ?>
      <div class="alert alert-success">
        Account created successfully.
        <a href="login.php">Login here</a>.
      </div>
    <?php else: ?>

      <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger py-2">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endforeach; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Employee Code</label>
          <input type="text" name="employee_code" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Work Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Create Password</label>
          <input type="password" name="password" class="form-control" minlength="6" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="confirm_password" class="form-control" minlength="6" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">
          Create Account
        </button>
      </form>
    <?php endif; ?>

    <p class="auth-footer-link">
      Already have an account?
      <a href="login.php">Login here</a>
    </p>
  </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
