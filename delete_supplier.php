<?php
include 'config.php';

header('Content-Type: application/json');


// ================= VALIDATION =================

if(!isset($_GET['id'])){

    echo json_encode([
        "status" => "error",
        "message" => "Supplier ID missing"
    ]);

    exit;
}

$id = intval($_GET['id']);


// ================= CHECK MEDICINES =================

$checkSql = "SELECT COUNT(*) AS total
             FROM Medicine
             WHERE supplier_id = ?";

$checkStmt = sqlsrv_query(
    $conn,
    $checkSql,
    array($id)
);

if($checkStmt){

    $row = sqlsrv_fetch_array(
        $checkStmt,
        SQLSRV_FETCH_ASSOC
    );

    if($row['total'] > 0){

        echo json_encode([
            "status" => "error",
            "message" => "Cannot delete supplier. Medicines exist."
        ]);

        exit;
    }
}


// ================= DELETE =================

$sql = "DELETE FROM Supplier
        WHERE supplier_id = ?";

$stmt = sqlsrv_query(
    $conn,
    $sql,
    array($id)
);

if($stmt){

    echo json_encode([
        "status" => "success",
        "message" => "Supplier deleted successfully"
    ]);

}else{

    echo json_encode([
        "status" => "error",
        "message" => "Delete failed"
    ]);
}
?>