<?php

$pageTitle = "Add Doctor";

include 'includes/sidebar.php';
include 'config.php';

$showSuccess = false;
$errorMsg = "";

$name = "";
$specialization = "";

if(isset($_POST['save'])){

    $name = trim($_POST['name']);
    $specialization = trim($_POST['specialization']);

    /* Validation */

    if($name == "" || $specialization == ""){

        $errorMsg = "All fields are required";

    }
    elseif(!preg_match("/^[a-zA-Z ]+$/", $name)){

        $errorMsg = "Doctor name must contain only letters";

    }
    else{

        $sql = "
        INSERT INTO Doctor
        (
            name,
            specialization
        )
        VALUES
        (
            ?, ?
        )";

        $params = array(
                        $name,
                        $specialization
                    );

        $stmt = sqlsrv_query(
                    $conn,
                    $sql,
                    $params
                 );

        if($stmt){

            $showSuccess = true;

        }else{

            $errorMsg = "Failed to add doctor";
        }
    }
}
?>

<div class="form-wrapper">

<div class="form-card">


<div class="form-title-box">

    <h3>
        Add Doctor
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
            name="save"
            class="btn btn-primary">

            Save Doctor

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

    title:'Success!',
    text:'Doctor added successfully',
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
