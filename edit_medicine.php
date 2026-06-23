```php
<?php

$pageTitle = "Edit Medicine";
include 'includes/sidebar.php';
include 'config.php';

$showSuccess = false;
$errorMsg = "";

/* Get Medicine Details */

if(isset($_GET['id'])){

    $id = $_GET['id'];

    $sql = "
    SELECT *
    FROM Medicine
    WHERE medicine_id = ?
    ";

    $params = array($id);

    $stmt = sqlsrv_query(
                $conn,
                $sql,
                $params
            );

    if(
        $stmt &&
        $row = sqlsrv_fetch_array(
                    $stmt,
                    SQLSRV_FETCH_ASSOC
               )
    ){

        $name = $row['name'];
        $price = $row['price'];
        $expiry = $row['expiry_date']->format('Y-m-d');

    }else{

        echo "
        <div class='alert alert-danger'>
            Medicine not found
        </div>";

        exit;
    }

}else{

    echo "
    <div class='alert alert-danger'>
        Invalid request
    </div>";

    exit;
}

/* Update Medicine */

if(isset($_POST['update'])){

    $id = $_POST['id'];

    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $expiry = $_POST['expiry'];

    if(
        $name == "" ||
        $price == "" ||
        $expiry == ""
    ){

        $errorMsg =
        "All fields are required";
    }
    elseif(
        !preg_match(
            "/^[a-zA-Z ]+$/",
            $name
        )
    ){

        $errorMsg =
        "Medicine name must contain only letters";
    }
    elseif($price <= 0){

        $errorMsg =
        "Price must be greater than zero";
    }
    elseif($expiry <= date('Y-m-d')){

        $errorMsg =
        "Expiry date must be a future date";
    }
    else{

        $sql = "
        UPDATE Medicine
        SET
            name = ?,
            price = ?,
            expiry_date = ?
        WHERE medicine_id = ?
        ";

        $params = array(
                        $name,
                        $price,
                        $expiry,
                        $id
                    );

        $stmt = sqlsrv_query(
                    $conn,
                    $sql,
                    $params
                 );

        if($stmt){

            $showSuccess = true;

        }else{

            $errors = sqlsrv_errors();

            $errorMsg =
            $errors[0]['message'];
        }
    }
}
?>

<div class="form-wrapper">

<div class="form-card">

    <div class="form-title-box">

        <h3>
            Edit Medicine
        </h3>

    </div>

    <form method="POST">

        <input
            type="hidden"
            name="id"
            value="<?php echo $id; ?>">

        <div class="mb-3">

            <label class="form-label">
                Medicine Name
            </label>

            <input
                type="text"
                name="name"
                class="form-control"
                value="<?php echo htmlspecialchars($name); ?>"
                required>

        </div>

        <div class="mb-3">

            <label class="form-label">
                Price
            </label>

            <input
                type="number"
                step="0.01"
                min="1"
                name="price"
                class="form-control"
                value="<?php echo htmlspecialchars($price); ?>"
                required>

        </div>

        <div class="mb-3">

            <label class="form-label">
                Expiry Date
            </label>

            <input
                type="date"
                name="expiry"
                class="form-control"
                value="<?php echo $expiry; ?>"
                min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                required>

        </div>

        <div class="d-flex gap-2 mt-3">

            <button
                type="submit"
                name="update"
                class="btn btn-primary">

                Update Medicine

            </button>

            <a
                href="medicines.php"
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

    text:'Medicine updated successfully',

    icon:'success',

    confirmButtonColor:'#2563EB'

}).then(() => {

    window.location =
    'medicines.php';

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
```
