<?php

include 'config.php';

if(isset($_GET['id'])){

    $id = intval($_GET['id']);

    $sql = "DELETE FROM Inventory
            WHERE inventory_id = ?";

    $stmt = sqlsrv_query(
        $conn,
        $sql,
        array($id)
    );
}

header("Location: inventory.php");
exit;
?>