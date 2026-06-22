<?php 
$pageTitle = "Patient History";
include 'includes/sidebar.php';
include 'config.php';

if(!isset($_GET['id'])){
    echo "<div class='alert alert-danger'>Invalid request</div>";
    exit;
}

$patient_id = $_GET['id'];

// get patient name
$sql = "SELECT name FROM Patient WHERE patient_id = ?";
$stmt = sqlsrv_query($conn, $sql, array($patient_id));
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

$patient_name = $row['name'];
?>

<!-- ================= HEADER ================= -->
<div class="mb-4">
    <h4>History - <?php echo htmlspecialchars($patient_name); ?></h4>
</div>

<!-- ================= TABLE ================= -->
<div class="card-box">
<div class="table-responsive">

<table class="table table-bordered align-middle">
<thead>
<tr>
    <th>Date</th>
    <th>Medicines</th>
</tr>
</thead>

<tbody>

<?php
$sql = "SELECT prescription_id, date 
        FROM Prescription 
        WHERE patient_id = ?
        ORDER BY date DESC";

$stmt = sqlsrv_query($conn, $sql, array($patient_id));

if($stmt){

    while($pres = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){

        $pid = $pres['prescription_id'];
        $date = $pres['date']->format('Y-m-d');

        echo "<tr>";
        echo "<td>$date</td>";

        echo "<td>";

        $sql2 = "SELECT m.name, pi.quantity
                 FROM Prescription_Items pi
                 JOIN Medicine m ON pi.medicine_id = m.medicine_id
                 WHERE pi.prescription_id = ?";

        $stmt2 = sqlsrv_query($conn, $sql2, array($pid));

        while($med = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)){
            echo "<div>
                    <span class='badge bg-primary'>".$med['name']."</span>
                    x ".$med['quantity']."
                  </div>";
        }

        echo "</td>";
        echo "</tr>";
    }

} else {
    echo "<tr><td colspan='2'>No history found</td></tr>";
}
?>

</tbody>
</table>

</div>
</div>

<?php include 'includes/footer.php'; ?>