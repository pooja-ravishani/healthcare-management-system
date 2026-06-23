<?php 
$pageTitle = "Edit Patient";
include 'includes/sidebar.php';
include 'config.php';

$showSuccess = false;
$errorMsg = "";

// ================= GET PATIENT =================
if(isset($_GET['id'])){
    $id = $_GET['id'];

    $sql = "SELECT * FROM Patient WHERE patient_id = ?";
    $params = array($id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        $name = $row['name'];
        $age = $row['age'];
        $gender = $row['gender'];
        $contact = $row['contact'];
    } else {
        echo "<div class='alert alert-danger'>Patient not found</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger'>Invalid request</div>";
    exit;
}

// ================= UPDATE =================
if(isset($_POST['update'])){

    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];
    $contact = trim($_POST['contact']);

    // 🔥 VALIDATION
    if($name == "" || $age <= 0 || $gender == "" || $contact == ""){
        $errorMsg = "Please fill all fields correctly";
    }
    elseif(!preg_match('/^[a-zA-Z ]+$/', $name)){
        $errorMsg = "Name must contain only letters";
    }
    elseif($age <= 0 || $age > 120){
        $errorMsg = "Invalid age";
    }
    elseif(!preg_match('/^[0-9]{10}$/', $contact)){
        $errorMsg = "Contact must be 10 digits";
    }
    else{

        $sql = "UPDATE Patient SET name=?, age=?, gender=?, contact=? WHERE patient_id=?";
        $params = array($name, $age, $gender, $contact, $id);

        $stmt = sqlsrv_query($conn, $sql, $params);

        if($stmt){
            $showSuccess = true;
        } else {
            $errors = sqlsrv_errors();
            $errorMsg = $errors[0]['message'];
        }
    }
}
?>

<!-- ================= FORM ================= -->
<div class="form-wrapper">

<div class="form-card">

    <div class="form-title-box">
        <h3>Edit Patient</h3>
    </div>

    <form method="POST">

        <input type="hidden"
               name="id"
               value="<?php echo $id; ?>">

        <div class="row">

            <!-- NAME -->
            <div class="col-md-6 mb-4">

                <label class="form-label">
                    Patient Name
                </label>

                <input type="text"
                       name="name"
                       value="<?php echo htmlspecialchars($name); ?>"
                       class="form-control"
                       required>

            </div>

            <!-- AGE -->
            <div class="col-md-6 mb-4">

                <label class="form-label">
                    Age
                </label>

                <input type="number"
                       name="age"
                       value="<?php echo $age; ?>"
                       class="form-control"
                       min="1"
                       required>

            </div>

            <!-- GENDER -->
            <div class="col-md-6 mb-4">

                <label class="form-label">
                    Gender
                </label>

                <select name="gender"
                        class="form-select"
                        required>

                    <option value="Male"
                    <?php if($gender=="Male") echo "selected"; ?>>
                        Male
                    </option>

                    <option value="Female"
                    <?php if($gender=="Female") echo "selected"; ?>>
                        Female
                    </option>

                </select>

            </div>

            <!-- CONTACT -->
            <div class="col-md-6 mb-4">

                <label class="form-label">
                    Contact Number
                </label>

                <input type="text"
                       name="contact"
                       value="<?php echo htmlspecialchars($contact); ?>"
                       class="form-control"
                       required>

            </div>

        </div>

        <!-- BUTTONS -->

        <div class="form-actions">

            <a href="patients.php"
               class="btn btn-secondary">
                Cancel
            </a>

            <button type="submit"
                    name="update"
                    class="btn btn-primary">
                Update Patient
            </button>

        </div>

    </form>

</div>

</div>

<?php include 'includes/footer.php'; ?>

<!-- ================= POPUPS ================= -->

<?php if($showSuccess): ?>
<script>
Swal.fire({
    title: 'Updated!',
    text: 'Patient updated successfully',
    icon: 'success',
    confirmButtonColor: '#2d65ff'
}).then(() => {
    window.location.href = 'patients.php';
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