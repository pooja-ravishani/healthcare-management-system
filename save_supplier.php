<?php
include 'config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $name = trim($_POST['name']);
    $contact = trim($_POST['contact']);

    // validation

    if(empty($name) || empty($contact)){

        echo "
        <script>
        alert('All fields are required');
        window.location='add_supplier.php';
        </script>
        ";
        exit;
    }

    // insert

    $sql = "INSERT INTO Supplier(name, contact)
            VALUES(?, ?)";

    $params = array($name, $contact);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if($stmt){

        echo "
        <script>

        alert('Supplier added successfully');

        window.location='suppliers.php';

        </script>
        ";

    }else{

        echo "
        <script>

        alert('Database error');

        window.location='add_supplier.php';

        </script>
        ";
    }

}else{

    header('Location: suppliers.php');
}
?>