<?php
include 'config.php';

header('Content-Type: application/json');

// validate
if(!isset($_GET['id'])){
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request"
    ]);
    exit;
}

$id = intval($_GET['id']);

$sql = "DELETE FROM Medicine WHERE medicine_id = ?";
$params = array($id);

$stmt = sqlsrv_query($conn, $sql, $params);

if($stmt){

    $rows = sqlsrv_rows_affected($stmt);

    if($rows > 0){
        echo json_encode([
            "status" => "success",
            "message" => "Medicine deleted successfully"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Medicine not found"
        ]);
    }

} else {

    $errors = sqlsrv_errors();
    $msg = "Error deleting medicine";

    if($errors && isset($errors[0]['message'])){
        $raw = $errors[0]['message'];

        // 🔥 FK ERROR HANDLE
        if(strpos($raw, 'REFERENCE constraint') !== false){
            $msg = "Cannot delete. Medicine is used in prescriptions.";
        }
    }

    echo json_encode([
        "status" => "error",
        "message" => $msg
    ]);
}
?>