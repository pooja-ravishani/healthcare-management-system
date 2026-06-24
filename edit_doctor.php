<?php

$pageTitle = "Edit Doctor";

include 'includes/sidebar.php';
include 'config.php';

$showSuccess = false;
$errorMsg = "";

if(!isset($_GET['id'])){

    header("Location: doctors.php");
    exit;
}

$id = intval($_GET['id']);

$sql = "
SELECT *
FROM Doctor
WHERE doctor_id = ?
";

$stmt = sqlsrv_query(
            $conn,
            $sql,
            array($id)
        );

if(!$stmt ||
   !($doctor = sqlsrv_fetch_array(
                    $stmt,
                    SQLSRV_FETCH_ASSOC
                )))
{
    die("Doctor not found");
}

$name = $doctor['name'];
$specialization = $doctor['specialization'];

if(isset($_POST['update'])){

    $name = trim($_POST['name']);
    $specialization = trim($_POST['specialization']);

    /* Validation */

    if($name == "" || $specialization == ""){

        $errorMsg = "All fields are required";
    }
    elseif(!preg_match("/^[a-zA-Z\s.]+$/", $name)){

        $errorMsg = "Doctor name must contain only letters";
    }
    else{

        $sql = "
        UPDATE Doctor
        SET
            name = ?,
            specialization = ?
        WHERE doctor_id = ?
        ";

        $params = array(
                        $name,
                        $specialization,
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

            $errorMsg = "Failed to update doctor";
        }
    }
}
?>

<div class="form-wrapper">

<div class="form-card">


<div class="form-title-box">

    <h3>
        Edit Doctor
    </h3>

</div>

<form method="POST">

    <div class="row">

        <div class="col-md-12 mb-3">

            <label class="form-label">
                Doctor Name
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
                Specialization
            </label>

            <input
                type="text"
                name="specialization"
                class="form-control"
                value="<?php echo htmlspecialchars($specialization); ?>"
                required>

        </div>

    </div>

    <div class="d-flex gap-2 mt-3">

        <button
            type="submit"
            name="update"
            class="btn btn-primary">

            Update Doctor

        </button>

        <a href="doctors.php"
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
    text:'Doctor updated successfully',
    icon:'success',
    confirmButtonColor:'#2563EB'

}).then(() => {

    window.location =
    'doctors.php';

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
