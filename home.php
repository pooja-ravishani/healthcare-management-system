<?php 
$pageTitle = "Healthcare Clinic";
include 'includes/sidebar.php';
include 'config.php';

// ================= COUNTS =================
$patientsCount = 0;
$medicinesCount = 0;
$inventoryCount = 0;
$doctorsCount = 0;
$suppliersCount = 0;
$prescriptionsCount = 0;

// ================= PATIENTS =================
$sql1 = "SELECT COUNT(*) AS total FROM Patient";
$stmt1 = sqlsrv_query($conn, $sql1);

if($stmt1 && $row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)){
    $patientsCount = $row['total'];
}

// ================= MEDICINES =================
$sql2 = "SELECT COUNT(*) AS total FROM Medicine";
$stmt2 = sqlsrv_query($conn, $sql2);

if($stmt2 && $row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)){
    $medicinesCount = $row['total'];
}

// ================= INVENTORY =================
$sql3 = "SELECT COUNT(*) AS total FROM Inventory";
$stmt3 = sqlsrv_query($conn, $sql3);

if($stmt3 && $row = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC)){
    $inventoryCount = $row['total'];
}

// ================= DOCTORS =================
$sql4 = "SELECT COUNT(*) AS total FROM Doctor";
$stmt4 = sqlsrv_query($conn, $sql4);

if($stmt4 && $row = sqlsrv_fetch_array($stmt4, SQLSRV_FETCH_ASSOC)){
    $doctorsCount = $row['total'];
}

// ================= SUPPLIERS =================
$sql5 = "SELECT COUNT(*) AS total FROM Supplier";
$stmt5 = sqlsrv_query($conn, $sql5);

if($stmt5 && $row = sqlsrv_fetch_array($stmt5, SQLSRV_FETCH_ASSOC)){
    $suppliersCount = $row['total'];
}

// ================= PRESCRIPTIONS =================
$sql6 = "SELECT COUNT(*) AS total FROM Prescription";
$stmt6 = sqlsrv_query($conn, $sql6);

if($stmt6 && $row = sqlsrv_fetch_array($stmt6, SQLSRV_FETCH_ASSOC)){
    $prescriptionsCount = $row['total'];
}
?>


<!-- ================= PAGE TITLE ================= -->

<div class="home-header">

    <h2 class="home-title">
        Healthcare Dashboard
    </h2>

    <p class="home-subtitle">
        Overview of patients, medicines, inventory and healthcare operations.
    </p>

</div>

<!-- ================= DASHBOARD CARDS ================= -->
<div class="row home-cards">

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card-box">
            <h2>Patients</h2>
            <p><?php echo $patientsCount; ?></p>
            <a href="patients.php" class="btn btn-primary w-100">Manage</a>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card-box">
            <h2>Medicines</h2>
            <p><?php echo $medicinesCount; ?></p>
            <a href="medicines.php" class="btn btn-primary w-100">Manage</a>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card-box">
            <h2>Inventory</h2>
            <p><?php echo $inventoryCount; ?></p>
            <a href="inventory.php" class="btn btn-primary w-100">Manage</a>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card-box">
            <h2>Doctors</h2>
            <p><?php echo $doctorsCount; ?></p>
            <a href="doctors.php" class="btn btn-primary w-100">Manage</a>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card-box">
            <h2>Suppliers</h2>
            <p><?php echo $suppliersCount; ?></p>
            <a href="suppliers.php" class="btn btn-primary w-100">Manage</a>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card-box">
            <h2>Prescriptions</h2>
            <p><?php echo $prescriptionsCount; ?></p>
            <a href="prescriptions.php" class="btn btn-primary w-100">Manage</a>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>