<?php

$serverName = "(localdb)\MSSQLLocalDB"; 
$connectionOptions = array(
    "Database" => "HealthcareDB"
);

// CONNECT
$conn = sqlsrv_connect($serverName, $connectionOptions);

if($conn === false){
    die(print_r(sqlsrv_errors(), true));
}

// echo "Connected Successfully!"; // test karanna puluwan

?>