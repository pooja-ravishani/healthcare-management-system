<?php

$pageTitle = "Update Inventory";

include 'includes/sidebar.php';
include 'config.php';

$showSuccess = false;
$errorMsg = "";

/* Get Inventory Data */

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id <= 0){

    header("Location: inventory.php");
    exit;
}

$sql = "
SELECT
    i.medicine_id,
    i.quantity,
    m.name
FROM Inventory i
INNER JOIN Medicine m
    ON i.medicine_id = m.medicine_id
WHERE i.medicine_id = ?
";

$stmt = sqlsrv_query(
            $conn,
            $sql,
            array($id)
        );

$row = sqlsrv_fetch_array(
            $stmt,
            SQLSRV_FETCH_ASSOC
        );

if(!$row){

    header("Location: inventory.php");
    exit;
}

$medicineName = $row['name'];
$quantity = $row['quantity'];

/* Update Inventory */

if(isset($_POST['update'])){

    $quantity = intval($_POST['quantity']);

    if($quantity < 0){

        $errorMsg = "Quantity cannot be negative";

    }else{

        $sql = "
        UPDATE Inventory
        SET quantity = ?
        WHERE medicine_id = ?
        ";

        $params = array(
                        $quantity,
                        $id
                    );

        $updateStmt = sqlsrv_query(
                            $conn,
                            $sql,
                            $params
                        );

        if($updateStmt){

            $showSuccess = true;

        }else{

            $errorMsg = "Failed to update inventory";
        }
    }
}
?>

<div class="form-wrapper">

<div class="form-card">


<div class="form-title-box">

    <h3>
        Update Inventory
    </h3>

</div>

<form method="POST">

    <div class="row">

        <div class="col-md-12 mb-3">

            <label class="form-label">
                Medicine
            </label>

            <input
                type="text"
                class="form-control"
                value="<?php echo htmlspecialchars($medicineName); ?>"
                readonly>

        </div>

        <div class="col-md-12 mb-3">

            <label class="form-label">
                Quantity
            </label>

            <input
                type="number"
                name="quantity"
                class="form-control"
                value="<?php echo $quantity; ?>"
                min="0"
                required>

        </div>

    </div>

    <div class="d-flex gap-2 mt-3">

        <button
            type="submit"
            name="update"
            class="btn btn-primary">

            Update Inventory

        </button>

        <a href="inventory.php"
           class="btn btn-secondary">

            Cancel

        </a>

    </div>

</form>


</div>

</div>

<?php include 'includes/footer.php'; ?>

<?php if($showSuccess): ?>

<script>

Swal.fire({

    title:'Success!',
    text:'Inventory updated successfully',
    icon:'success',
    confirmButtonColor:'#2563EB'

}).then(() => {

    window.location =
    'inventory.php';

});

</script>

<?php endif; ?>

<?php if($errorMsg != ""): ?>

<script>

Swal.fire({

    title:'Error!',
    text:'<?php echo $errorMsg; ?>',
    icon:'error'

});

</script>

<?php endif; ?>
