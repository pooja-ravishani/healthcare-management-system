<?php
include 'config.php';

if(!isset($_GET['id'])){
    header("Location: prescriptions.php?error=Invalid request");
    exit;
}

$id = intval($_GET['id']);

sqlsrv_begin_transaction($conn);

try{

    // 🔥 restore stock
    $items = sqlsrv_query($conn,
        "SELECT medicine_id, quantity FROM Prescription_Items WHERE prescription_id = ?",
        [$id]
    );

    while($row = sqlsrv_fetch_array($items, SQLSRV_FETCH_ASSOC)){
        sqlsrv_query($conn,
            "UPDATE Inventory SET quantity = quantity + ? WHERE medicine_id = ?",
            [$row['quantity'], $row['medicine_id']]
        );
    }

    // delete items
    sqlsrv_query($conn,
        "DELETE FROM Prescription_Items WHERE prescription_id = ?",
        [$id]
    );

    // delete prescription
    sqlsrv_query($conn,
        "DELETE FROM Prescription WHERE prescription_id = ?",
        [$id]
    );

    sqlsrv_commit($conn);

    header("Location: prescriptions.php?success=Deleted successfully");
    exit;

}catch(Exception $e){

    sqlsrv_rollback($conn);

    header("Location: prescriptions.php?error=Delete failed");
    exit;
}
?>