<?php
include 'config.php';

$sql = "SELECT * FROM vw_low_stock";
$stmt = sqlsrv_query($conn, $sql);
?>

<h3>⚠️ Low Stock Medicines</h3>

<table class="table table-bordered">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Quantity</th>
</tr>

<?php while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>

<tr style="background:#ffe5e5;">
    <td><?php echo $row['medicine_id']; ?></td>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['quantity']; ?></td>
</tr>

<?php endwhile; ?>

</table>