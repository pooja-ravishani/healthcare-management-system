<?php

include '../config.php';

// ================= VALIDATION =================

if(
    empty($_POST['name']) ||
    empty($_POST['age']) ||
    empty($_POST['gender']) ||
    empty($_POST['contact'])
){
    header("Location: add_patient.php?error=All fields are required");
    exit;
}

// ================= GET FORM DATA =================

$name    = trim($_POST['name']);
$age     = intval($_POST['age']);
$gender  = trim($_POST['gender']);
$contact = trim($_POST['contact']);

// ================= INSERT QUERY =================

$sql = "INSERT INTO Patient (name, age, gender, contact)
        VALUES (?, ?, ?, ?)";

$params = array(
    $name,
    $age,
    $gender,
    $contact
);

$stmt = sqlsrv_query($conn, $sql, $params);

// ================= RESULT =================

if($stmt){

    header("Location: patients.php?success=Patient added successfully");

}else{

    header("Location: add_patient.php?error=Failed to add patient");
}

?>