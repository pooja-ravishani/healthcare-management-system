<?php
session_start();

// 🔥 DEBUG (remove later)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 🔐 if already logged in → redirect safely
if(isset($_SESSION['user_id'])){

    $role = $_SESSION['role'] ?? '';

    if($role === 'admin' && file_exists('admin_dashboard.php')){
        header("Location: admin_dashboard.php");
        exit;
    }

    if(file_exists('home.php')){
        header("Location: home.php");
        exit;
    }

    // fallback (if files missing)
    echo "<h3 style='color:red;text-align:center;margin-top:100px'>
            Dashboard file not found
          </h3>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- 🔥 CSS (CHECK PATH) -->
    <link rel="stylesheet" href="/healthcare-system/css/style.css">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="auth-body">

<div class="auth-wrapper">

    <div class="auth-card">

        <!-- HEADER -->
        <div class="form-header text-center">
            🔐 Login
        </div>

        <!-- FORM -->
        <form method="POST" action="login_process.php">

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control"
                       placeholder="name@healthcare.lk" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Enter password" required>
            </div>

            <button class="btn btn-blue w-100">Login</button>

        </form>

        <!-- LINK -->
        <div class="text-center mt-3">
            No account? <a href="register.php">Create Account</a>
        </div>

    </div>

</div>

</body>
</html>