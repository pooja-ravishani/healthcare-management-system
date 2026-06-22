<?php

$pageTitle = "Edit Supplier";

include 'includes/sidebar.php';
include 'config.php';

$showSuccess = false;
$errorMsg = "";

/* ================= GET SUPPLIER ================= */

if(!isset($_GET['id'])){

    header("Location: suppliers.php");
    exit;
}

$id = intval($_GET['id']);

$sql = "
SELECT *
FROM Supplier
WHERE supplier_id = ?
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

    header("Location: suppliers.php");
    exit;
}

$name = $row['name'];
$contact = $row['contact'];

/* ================= UPDATE ================= */

if(isset($_POST['update'])){

    $name = trim($_POST['name']);
    $contact = trim($_POST['contact']);

    if($name == "" || $contact == ""){

        $errorMsg = "All fields are required";

    }
    elseif(!preg_match("/^[a-zA-Z ]+$/", $name)){

        $errorMsg = "Supplier name must contain only letters";

    }
    elseif(!preg_match('/^[0-9]{10}$/', $contact)){

        $errorMsg = "Contact number must contain 10 digits";

    }
    else{

        $sql = "
        UPDATE Supplier
        SET
            name = ?,
            contact = ?
        WHERE supplier_id = ?
        ";

        $params = array(
                        $name,
                        $contact,
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

            $errorMsg = "Failed to update supplier";
        }
    }
}
?>

<div class="form-wrapper">

<div class="form-card">


<div class="form-title-box">

    <h3>
        Edit Supplier
    </h3>

</div>

<form method="POST">

    <div class="row">

        <div class="col-md-12 mb-3">

            <label class="form-label">
                Supplier Name
            </label>

            <input
                type="text"
                name="name"
                class="form-control"
                value="<?php echo htmlspecialchars($name); ?>"
                required>

        </div>

        <div class="col-md-12 mb-3">

            <label class="form-label">
                Contact Number
            </label>

            <input
                type="text"
                name="contact"
                class="form-control"
                value="<?php echo htmlspecialchars($contact); ?>"
                required>

        </div>

    </div>

    <div class="d-flex gap-2 mt-3">

        <button
            type="submit"
            name="update"
            class="btn btn-primary">

            Update Supplier

        </button>

        <a href="suppliers.php"
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

    title:'Updated!',
    text:'Supplier updated successfully',
    icon:'success',
    confirmButtonColor:'#2563EB'

}).then(() => {

    window.location =
    'suppliers.php';

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
