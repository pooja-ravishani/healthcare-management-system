<?php 
$pageTitle = "Add Patient";
include 'includes/sidebar.php';
include 'config.php';

$showSuccess = false;
$errorMsg = "";

if(isset($_POST['save'])){

    $name = trim($_POST['name']);
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];
    $contact = trim($_POST['contact']);

    // Validation

    if($name == "" || $age <= 0 || $gender == "" || $contact == ""){

        $errorMsg = "Please fill all fields correctly";

    }
    elseif(!preg_match('/^[a-zA-Z ]+$/', $name)){

        $errorMsg = "Name must contain only letters";

    }
    elseif($age > 120){

        $errorMsg = "Invalid age";

    }
    elseif(!preg_match('/^[0-9]{10}$/', $contact)){

        $errorMsg = "Contact must be 10 digits";

    }
    else{

        $sql = "
        INSERT INTO Patient
        (
            name,
            age,
            gender,
            contact
        )
        VALUES
        (
            ?, ?, ?, ?
        )";

        $params = array(
            $name,
            $age,
            $gender,
            $contact
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

            if($errors){
                $errorMsg = $errors[0]['message'];
            }else{
                $errorMsg = "Failed to add patient";
            }
        }
    }
}
?>

<div class="form-wrapper">

<div class="form-card">

<div class="form-title-box">

    <h3>
        Add Patient
    </h3>

</div>

<form method="POST">

    <div class="row">

        <div class="col-md-6 mb-3">

            <label class="form-label">
                Patient Name
            </label>

            <input type="text"
                   name="name"
                   class="form-control"
                   required>

        </div>

        <div class="col-md-6 mb-3">

            <label class="form-label">
                Age
            </label>

            <input type="number"
                   name="age"
                   class="form-control"
                   min="1"
                   max="120"
                   required>

        </div>

        <div class="col-md-6 mb-3">

            <label class="form-label">
                Gender
            </label>

            <select name="gender"
                    class="form-select"
                    required>

                <option value="">
                    Select Gender
                </option>

                <option value="Male">
                    Male
                </option>

                <option value="Female">
                    Female
                </option>

            </select>

        </div>

        <div class="col-md-6 mb-3">

            <label class="form-label">
                Contact Number
            </label>

            <input type="text"
                   name="contact"
                   class="form-control"
                   maxlength="10"
                   required>

        </div>

    </div>

    <div class="d-flex gap-2 mt-3">

        <button type="submit"
                name="save"
                class="btn btn-primary">

            Save Patient

        </button>

        <a href="patients.php"
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

    text: 'Patient added successfully',

    icon: 'success',

    confirmButtonColor: '#2563EB'

}).then(() => {

    window.location.href =
    'patients.php';

});

</script>

<?php endif; ?>

<?php if($errorMsg != ""): ?>

<script>

Swal.fire({

    title: 'Error!',

    text: '<?php echo addslashes($errorMsg); ?>',

    icon: 'error'

});

</script>

<?php endif; ?>
