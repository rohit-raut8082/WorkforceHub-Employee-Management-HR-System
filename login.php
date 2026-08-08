<?php
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/models/user.php";

if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: employee/profile.php");
    }
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required.";
    } else {
        $result = getUserByEmail($conn, $email);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {
                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];
                $_SESSION["employee_id"] = $user["employee_id"];

                if ($user["role"] === "admin") {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: employee/profile.php");
                }
                exit();
            }
        }

        $errors[] = "Invalid email or password.";
    }
}

$pageTitle = "Login";
require_once __DIR__ . "/includes/header.php";
?>

<div class="auth-shell">
  <div class="auth-card">
    <div class="auth-brand"><i class="fa-solid fa-building-user"></i>WorkforceHub</div>
    <p class="auth-sub">Sign in to manage your workplace</p>

    <?php foreach ($errors as $error): ?>
      <div class="alert alert-danger py-2">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endforeach; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Email address</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <p class="auth-footer-link">
      Don't have an account?
      <a href="register.php">Register here</a>
    </p>

    <div class="demo-hint">
      <strong>Demo Credentials:</strong><br>
      Admin: admin@ems.com / admin123<br>
      Employee: asha.mehta@example.com / emp123
    </div>
  </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
