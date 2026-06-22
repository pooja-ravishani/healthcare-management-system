<?php

include 'config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $medicine_id = intval($_POST['medicine_id']);
    $quantity = intval($_POST['quantity']);

    // Check whether medicine already exists

    $checkSql = "
    SELECT inventory_id
    FROM Inventory
    WHERE medicine_id = ?
    ";

    $checkParams = array($medicine_id);

    $checkStmt = sqlsrv_query(
                    $conn,
                    $checkSql,
                    $checkParams
                 );

    if(
        $checkStmt &&
        sqlsrv_fetch_array(
            $checkStmt,
            SQLSRV_FETCH_ASSOC
        )
    ){

        // Update existing stock

        $sql = "
        UPDATE Inventory
        SET quantity = quantity + ?
        WHERE medicine_id = ?
        ";

        $params = array(
                        $quantity,
                        $medicine_id
                    );

    }else{

        // Insert new inventory record

        $sql = "
        INSERT INTO Inventory
        (
            medicine_id,
            quantity
        )
        VALUES
        (
            ?, ?
        )
        ";

        $params = array(
                        $medicine_id,
                        $quantity
                    );
    }

    $stmt = sqlsrv_query(
                $conn,
                $sql,
                $params
            );

    if($stmt){

        header("Location: inventory.php");
        exit;

    }else{

        echo "<pre>";
        print_r(sqlsrv_errors());
        echo "</pre>";
    }
}
?>
