<?php
include 'config.php';

header('Content-Type: application/json');


// ================= VALIDATION =================

if(!isset($_GET['id'])){

    echo json_encode([
        "status" => "error",
        "message" => "Doctor ID missing"
    ]);
    exit;
}

$id = intval($_GET['id']);


// ================= CHECK DOCTOR EXISTS =================

$doctorSql = "SELECT * FROM Doctor WHERE doctor_id = ?";
$doctorStmt = sqlsrv_query($conn, $doctorSql, array($id));

if(!$doctorStmt || !sqlsrv_has_rows($doctorStmt)){

    echo json_encode([
        "status" => "error",
        "message" => "Doctor not found"
    ]);
    exit;
}


// ================= CHECK PRESCRIPTIONS =================

$checkSql = "SELECT COUNT(*) AS total
             FROM Prescription
             WHERE doctor_id = ?";

$checkStmt = sqlsrv_query($conn, $checkSql, array($id));

if($checkStmt){

    $row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);

    if($row['total'] > 0){

        echo json_encode([
            "status" => "error",
            "message" => "Cannot delete doctor. Prescriptions exist."
        ]);
        exit;
    }

}else{

    echo json_encode([
        "status" => "error",
        "message" => "Validation failed"
    ]);
    exit;
}


// ================= DELETE DOCTOR =================

$sql = "DELETE FROM Doctor WHERE doctor_id = ?";

$stmt = sqlsrv_query($conn, $sql, array($id));

if($stmt){

    echo json_encode([
        "status" => "success",
        "message" => "Doctor deleted successfully"
    ]);

}else{

    $errors = sqlsrv_errors();

    echo json_encode([
        "status" => "error",
        "message" => $errors[0]['message']
    ]);
}
?>