<?php
session_start();
include 'config.php';

$email = $_POST['email'];
$password = $_POST['password'];

$user = sqlsrv_query($conn,"SELECT * FROM Users WHERE email=?",[$email]);
$data = sqlsrv_fetch_array($user, SQLSRV_FETCH_ASSOC);

if($data && password_verify($password,$data['password'])){

    $_SESSION['user_id'] = $data['user_id'];
    $_SESSION['name'] = $data['name'];
    $_SESSION['role'] = $data['role'];

    // 🔥 DIRECT REDIRECT (NO LOOP)
    if($data['role'] === 'admin'){
        header("Location: admin_dashboard.php");
    } else {
        header("Location: home.php");
    }
    exit;

}else{
    header("Location: index.php?error=Invalid login");
    exit;
}
?>