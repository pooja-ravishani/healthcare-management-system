<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit;
}

// role function
function requireRole($roles){
    if(!in_array($_SESSION['role'], (array)$roles)){
        echo "<script>alert('Access denied');window.location='home.php';</script>";
        exit;
    }
}