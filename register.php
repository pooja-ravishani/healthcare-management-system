<?php
include 'config.php';

$success = false;
$error = "";

// ================= REGISTER =================
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if(empty($name) || empty($email) || empty($password)){
        $error = "All fields are required";
    }
    elseif(!preg_match("/^[a-zA-Z0-9._%+-]+@healthcare\.lk$/", $email)){
        $error = "Email must be like name@healthcare.lk";
    }
    else{

        $check = sqlsrv_query($conn,"SELECT * FROM Users WHERE email=?",[$email]);

        if(sqlsrv_fetch_array($check)){
            $error = "Email already exists";
        }
        else{

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $insert = sqlsrv_query($conn,
                "INSERT INTO Users (name,email,password,role)
                 VALUES (?,?,?,?)",
                [$name,$email,$hashed,$role]
            );

            if($insert){
                $success = true;
            } else {
                $error = "Registration failed";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 🔥 USE YOUR STYLE FILE -->
    <link rel="stylesheet" href="/healthcare-system/css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="auth-body">

<div class="auth-wrapper">

    <div class="auth-card">

        <div class="form-header text-center">
            📝 Create Account
        </div>

        <form method="POST">

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control"
                       placeholder="name@healthcare.lk" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-select">
                    <option value="staff">Staff</option>
                    <option value="doctor">Doctor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button class="btn btn-blue w-100">Register</button>

            <div class="text-center mt-3">
                <a href="index.php">Already have an account? Login</a>
            </div>

        </form>

    </div>

</div>

<?php if($success): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Registered!',
    text: 'Account created successfully'
}).then(() => {
    window.location = 'index.php';
});
</script>
<?php endif; ?>

<?php if($error): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: '<?php echo $error; ?>'
});
</script>
<?php endif; ?>

</body>
</html>