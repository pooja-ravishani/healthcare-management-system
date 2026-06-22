<?php
include 'config.php';

header('Content-Type: application/json');

$id = intval($_GET['id']);

$sql = "SELECT COUNT(*) AS total FROM Prescription WHERE patient_id = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

echo json_encode([
    "count" => $row['total']
]);
?>