<?php
$pageTitle = "Add Medicine";
include 'includes/sidebar.php';
include 'config.php';

$showSuccess = false;
$errorMsg = "";

if(isset($_POST['submit'])){

    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $expiry = $_POST['expiry'];

    // VALIDATION

    if($name == "" || $price <= 0 || $expiry == ""){

        $errorMsg = "Please fill all fields correctly";

    }
    elseif(!preg_match('/^[a-zA-Z ]+$/', $name)){

        $errorMsg = "Medicine name must contain only letters";

    }
    elseif($price <= 0){

        $errorMsg = "Price must be greater than 0";

    }
    elseif($expiry <= date('Y-m-d')){

        $errorMsg = "Expiry date must be a future date";

    }
    else{

        $sql = "
        INSERT INTO Medicine
        (
            name,
            price,
            expiry_date
        )
        VALUES
        (
            ?, ?, ?
        )";

        $params = array(
            $name,
            $price,
            $expiry
        );

        $stmt = sqlsrv_query(
                    $conn,
                    $sql,
                    $params
                );

        if($stmt){

            $showSuccess = true;

        }else{

            $error = sqlsrv_errors();
            $errorMsg = $error[0]['message'];
        }
    }
}
?>

<div class="form-wrapper">

<div class="form-card">

<div class="form-title-box">

    <h3>
        Add Medicine
    </h3>

</div>

<form method="POST">

    <div class="row">

        <div class="col-md-6 mb-3">

            <label class="form-label">
                Medicine Name
            </label>

            <input type="text"
                   name="name"
                   class="form-control"
                   required>

        </div>

        <div class="col-md-6 mb-3">

            <label class="form-label">
                Price (Rs.)
            </label>

            <input type="number"
                   step="0.01"
                   min="1"
                   name="price"
                   class="form-control"
                   required>

        </div>

        <div class="col-md-12 mb-3">

            <label class="form-label">
                Expiry Date
            </label>

            <input type="date"
                   name="expiry"
                   class="form-control"
                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                   required>

        </div>

    </div>

    <div class="d-flex gap-2 mt-3">

        <button type="submit"
                name="submit"
                class="btn btn-primary">

            Save Medicine

        </button>

        <a href="medicines.php"
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
    title: 'Success!',
    text: 'Medicine added successfully',
    icon: 'success',
    confirmButtonColor: '#2563EB'
}).then(() => {
    window.location.href = 'medicines.php';
});
</script>

<?php endif; ?>

<?php if($errorMsg != ""): ?>

<script>
Swal.fire({
    title: 'Error!',
    text: '<?php echo $errorMsg; ?>',
    icon: 'error'
});
</script>

<?php endif; ?>
