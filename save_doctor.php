<?php
include 'config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $name = trim($_POST['name']);
    $specialization = trim($_POST['specialization']);

    // ================= VALIDATION =================

    if(empty($name) || empty($specialization)){

        echo "
        <script>
            alert('All fields are required');
            window.location='add_doctor.php';
        </script>
        ";
        exit;
    }

    // ================= INSERT =================

    $sql = "INSERT INTO Doctor(name, specialization)
            VALUES(?, ?)";

    $params = array($name, $specialization);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if($stmt){

        echo "
        <script>
            alert('Doctor added successfully');
            window.location='doctors.php';
        </script>
        ";

    }else{

        echo "
        <script>
            alert('Database error');
            window.location='add_doctor.php';
        </script>
        ";
    }

}else{

    header("Location: doctors.php");
}
?>