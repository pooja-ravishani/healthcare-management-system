<?php
include 'config.php';

// 🔥 return JSON always
header('Content-Type: application/json');

// ================= VALIDATE =================
if(!isset($_GET['id']) || empty($_GET['id'])){
    echo json_encode([
        "status" => "error",
        "message" => "Invalid patient ID"
    ]);
    exit;
}

// 🔒 sanitize id
$id = intval($_GET['id']);

// ================= DELETE =================
$sql = "DELETE FROM Patient WHERE patient_id = ?";
$params = array($id);

$stmt = sqlsrv_query($conn, $sql, $params);

// ================= RESPONSE =================
if($stmt){

    $rows = sqlsrv_rows_affected($stmt);

    if($rows > 0){
        echo json_encode([
            "status" => "success",
            "message" => "Patient deleted successfully"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Patient not found"
        ]);
    }

} else {

    $errors = sqlsrv_errors();

    // 🔥 safe default message
    $msg = "Database error";

    if($errors && isset($errors[0]['message'])){
        $raw = $errors[0]['message'];

        // 🔥 FK error friendly message
        if(strpos($raw, 'REFERENCE constraint') !== false){
            $msg = "Cannot delete patient. This patient has prescriptions.";
        } else {
            $msg = $raw; // optional (dev mode)
        }
    }

    echo json_encode([
        "status" => "error",
        "message" => $msg
    ]);
}
?>