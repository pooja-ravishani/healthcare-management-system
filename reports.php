<?php
$pageTitle = "Reports";
include 'includes/sidebar.php';
include 'config.php';


// low stock
$low = sqlsrv_query($conn,
    "SELECT m.name, i.quantity
     FROM Inventory i
     JOIN Medicine m ON i.medicine_id = m.medicine_id
     WHERE i.quantity <= 10"
);
?>

<div class="admin-main-content">

<h4 class="mb-4">📊 Reports</h4>


<!-- 🔥 LOW STOCK -->
<div class="card-box">
    <h5>⚠️ Low Stock Medicines</h5>

    <table class="table table-bordered">
        <tr>
            <th>Medicine</th>
            <th>Stock</th>
        </tr>

        <?php while($row = sqlsrv_fetch_array($low, SQLSRV_FETCH_ASSOC)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td style="color:red;"><?php echo $row['quantity']; ?></td>
        </tr>
        <?php endwhile; ?>

    </table>
</div>

</div>

<?php include 'includes/footer.php'; ?>