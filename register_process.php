<?php
include 'config.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// default role
$role = "staff";

// email validation
if(!preg_match("/^[a-zA-Z0-9._%+-]+@healthcare\.lk$/", $email)){
    header("Location: register.php?error=Invalid email");
    exit;
}

// insert with role
sqlsrv_query($conn,
    "INSERT INTO Users (name,email,password,role) VALUES (?,?,?,?)",
    [$name,$email,$password,$role]
);

header("Location: index.php?success=Account created");