<?php
$pageTitle = "Admin Dashboard";
include 'includes/sidebar.php';
include 'config.php';

// ================= COUNTS =================
$patients = sqlsrv_fetch_array(sqlsrv_query($conn,"SELECT COUNT(*) AS c FROM Patient"), SQLSRV_FETCH_ASSOC)['c'];

$medicines = sqlsrv_fetch_array(sqlsrv_query($conn,"SELECT COUNT(*) AS c FROM Medicine"), SQLSRV_FETCH_ASSOC)['c'];

$inventory = sqlsrv_fetch_array(sqlsrv_query($conn,"SELECT COUNT(*) AS c FROM Inventory"), SQLSRV_FETCH_ASSOC)['c'];

$doctors = sqlsrv_fetch_array(sqlsrv_query($conn,"SELECT COUNT(*) AS c FROM Doctor"), SQLSRV_FETCH_ASSOC)['c'];

$prescriptions = sqlsrv_fetch_array(sqlsrv_query($conn,"SELECT COUNT(*) AS c FROM Prescription"), SQLSRV_FETCH_ASSOC)['c'];

$low = sqlsrv_fetch_array(sqlsrv_query($conn,"SELECT COUNT(*) AS c FROM Inventory WHERE quantity <= 10"), SQLSRV_FETCH_ASSOC)['c'];

$medium = sqlsrv_fetch_array(sqlsrv_query($conn,"SELECT COUNT(*) AS c FROM Inventory WHERE quantity > 10 AND quantity <= 30"), SQLSRV_FETCH_ASSOC)['c'];

$ok = sqlsrv_fetch_array(sqlsrv_query($conn,"SELECT COUNT(*) AS c FROM Inventory WHERE quantity > 30"), SQLSRV_FETCH_ASSOC)['c'];


// ================= TOP MEDICINES =================
$topMedQuery = sqlsrv_query($conn,
    "SELECT TOP 5 
        m.name, 
        SUM(pi.quantity) AS total_used
     FROM Prescription_Items pi
     JOIN Medicine m 
        ON pi.medicine_id = m.medicine_id
     GROUP BY m.name
     ORDER BY total_used DESC"
);

$medNames = [];
$medTotals = [];

while($row = sqlsrv_fetch_array($topMedQuery, SQLSRV_FETCH_ASSOC)){

    $medNames[] = $row['name'];

    $medTotals[] = (int)$row['total_used'];
}


// ================= EXPIRY =================
$expiry = sqlsrv_query($conn,
    "SELECT TOP 5 
        name, 
        expiry_date
     FROM Medicine
     WHERE expiry_date BETWEEN GETDATE() 
     AND DATEADD(DAY, 30, GETDATE())
     ORDER BY expiry_date ASC"
);


// ================= RECENT =================
$recent = sqlsrv_query($conn,
    "SELECT TOP 5
        p.prescription_id,
        pt.name AS patient,
        d.name AS doctor,
        p.date
     FROM Prescription p
     JOIN Patient pt 
        ON p.patient_id = pt.patient_id
     JOIN Doctor d 
        ON p.doctor_id = d.doctor_id
     ORDER BY p.prescription_id DESC"
);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


<div class="admin-main-content">

<!-- ================= TITLE ================= -->
<h2 class="admin-title">
    📊 Admin Dashboard
</h2>


<!-- ================= SMALL STATS ================= -->
<div class="row g-3 mb-4">

    <?php
    function statCard($icon, $label, $value){

        echo "

        <div class='col-md-3'>

            <div class='mini-stat-card'>

                <div class='mini-icon'>
                    <i class='$icon'></i>
                </div>

                <div>

                    <div class='mini-label'>
                        $label
                    </div>

                    <div class='mini-value'>
                        $value
                    </div>

                </div>

            </div>

        </div>

        ";
    }

    statCard("bi bi-people-fill", "Patients", $patients);

    statCard("bi bi-capsule-pill", "Medicines", $medicines);

    statCard("bi bi-box-seam", "Inventory", $inventory);

    statCard("bi bi-person-badge-fill", "Doctors", $doctors);
    ?>

</div>


<!-- ================= BIG STATS ================= -->
<div class="row g-3 mb-4">

    <!-- TOTAL -->
    <div class="col-md-4">

        <div class="dashboard-info-card">

            <div class="dash-icon blue">
                <i class="bi bi-file-earmark-medical"></i>
            </div>

            <h6>Total Prescriptions</h6>

            <h2>
                <?php echo $prescriptions; ?>
            </h2>

        </div>

    </div>


    <!-- LOW STOCK -->
    <div class="col-md-4">

        <div class="dashboard-info-card">

            <div class="dash-icon red">
                <i class="bi bi-exclamation-triangle"></i>
            </div>

            <h6>Low Stock</h6>

            <h2 class="text-danger">
                <?php echo $low; ?>
            </h2>

            <a href="reports.php"
               class="btn btn-danger btn-sm mt-2">
               More Details
            </a>

        </div>

    </div>


    <!-- STATUS -->
    <div class="col-md-4">

        <div class="dashboard-info-card">

            <div class="dash-icon green">
                <i class="bi bi-check-circle"></i>
            </div>

            <h6>System Status</h6>

            <h2 class="text-success">
                Active
            </h2>

        </div>

    </div>

</div>


<!-- ================= CHARTS ================= -->
<div class="row g-4">

    <!-- PIE -->
    <div class="col-md-5">

        <div class="card-box">

            <h5 class="chart-title">
                📦 Inventory Distribution
            </h5>

            <div class="pie-chart-box">
                <canvas id="inventoryChart"></canvas>
            </div>

        </div>

    </div>


    <!-- BAR -->
    <div class="col-md-7">

        <div class="card-box">

            <h5 class="chart-title">
                💊 Top Used Medicines
            </h5>

            <div class="bar-chart-box">
                <canvas id="topMedChart"></canvas>
            </div>

        </div>

    </div>

</div>


<!-- ================= TABLES ================= -->
<div class="row g-4 mt-2">

    <!-- EXPIRY -->
    <div class="col-md-6">

        <div class="card-box">

            <h5 class="chart-title text-danger">
                ⏰ Expiring Soon
            </h5>

            <table class="table table-bordered table-sm table-red">

                <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Expiry Date</th>
                </tr>
                </thead>

                <tbody>

                <?php

                $hasExpiry = false;

                while($row = sqlsrv_fetch_array($expiry, SQLSRV_FETCH_ASSOC)){

                    $hasExpiry = true;

                    $date = $row['expiry_date']
                            ? $row['expiry_date']->format('Y-m-d')
                            : '';

                    echo "

                    <tr>

                        <td>".htmlspecialchars($row['name'])."</td>

                        <td>$date</td>

                    </tr>

                    ";
                }

                if(!$hasExpiry){

                    echo "

                    <tr>

                        <td colspan='2'
                            class='text-center text-muted'>

                            No medicines expiring soon

                        </td>

                    </tr>

                    ";
                }

                ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- RECENT -->
    <div class="col-md-6">

        <div class="card-box">

            <h5 class="chart-title">
                🧾 Recent Prescriptions
            </h5>

            <table class="table table-bordered table-sm">

                <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Date</th>
                </tr>
                </thead>

                <tbody>

                <?php

                while($row = sqlsrv_fetch_array($recent, SQLSRV_FETCH_ASSOC)){

                    $date = $row['date']
                            ? $row['date']->format('Y-m-d')
                            : '';

                    echo "

                    <tr>

                        <td>{$row['prescription_id']}</td>

                        <td>{$row['patient']}</td>

                        <td>{$row['doctor']}</td>

                        <td>$date</td>

                    </tr>

                    ";
                }

                ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</div>


<?php include 'includes/footer.php'; ?>


<script>

// ================= PIE =================
new Chart(document.getElementById('inventoryChart'), {

    type: 'pie',

    data: {

        labels: ['Low', 'Medium', 'Healthy'],

        datasets: [{

            data: [
                <?php echo $low; ?>,
                <?php echo $medium; ?>,
                <?php echo $ok; ?>
            ],

            backgroundColor: [
                '#ef4444',
                '#facc15',
                '#22c55e'
            ]

        }]
    },

    options: {

        responsive:true,

        maintainAspectRatio:false
    }
});


// ================= BAR =================
new Chart(document.getElementById('topMedChart'), {

    type: 'bar',

    data: {

        labels: <?php echo json_encode($medNames); ?>,

        datasets: [{

            data: <?php echo json_encode($medTotals); ?>,

            backgroundColor:'#2563eb',

            borderRadius:10
        }]
    },

    options: {

        responsive:true,

        maintainAspectRatio:false,

        plugins:{
            legend:{
                display:false
            }
        },

        scales:{
            y:{
                beginAtZero:true
            }
        }
    }
});

</script>