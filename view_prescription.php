<?php
$pageTitle = "View Prescription";

include 'includes/sidebar.php';
include 'config.php';

/* ================= GET ID ================= */

if(!isset($_GET['id'])){

    echo "
    <div class='alert alert-danger'>
        Invalid request
    </div>";

    exit;
}

$id = intval($_GET['id']);

/* ================= PRESCRIPTION DETAILS ================= */

$sql = "
SELECT
    p.date,
    pa.name AS patient_name,
    d.name AS doctor_name
FROM Prescription p
INNER JOIN Patient pa
    ON p.patient_id = pa.patient_id
INNER JOIN Doctor d
    ON p.doctor_id = d.doctor_id
WHERE p.prescription_id = ?
";

$stmt = sqlsrv_query(
            $conn,
            $sql,
            array($id)
        );

$data = sqlsrv_fetch_array(
            $stmt,
            SQLSRV_FETCH_ASSOC
        );

if(!$data){

    echo "
    <div class='alert alert-danger'>
        Prescription not found
    </div>";

    exit;
}

/* ================= MEDICINES ================= */

$sqlItems = "
SELECT
    m.name,
    pi.quantity
FROM Prescription_Items pi
INNER JOIN Medicine m
    ON pi.medicine_id = m.medicine_id
WHERE pi.prescription_id = ?
";

$stmtItems = sqlsrv_query(
                $conn,
                $sqlItems,
                array($id)
             );
?>

<!-- ================= PAGE HEADER ================= -->

<div class="page-top">

    <h2 class="page-heading">
        View Prescription
    </h2>

</div>

<!-- ================= PRESCRIPTION CARD ================= -->

<div
    class="table-card prescription-view"
    id="printArea">

    <!-- HEADER -->

    <div class="text-center mb-4">

        <h2 class="prescription-title">
            Healthcare Clinic
        </h2>

        <p class="prescription-subtitle">
            Prescription Details
        </p>

        <hr>

    </div>

    <!-- DETAILS -->

    <div class="row mb-4">

        <div class="col-md-6">

            <p class="detail-text">

                <strong>Patient :</strong>

                <?php
                echo htmlspecialchars(
                    $data['patient_name']
                );
                ?>

            </p>

            <p class="detail-text">

                <strong>Doctor :</strong>

                <?php
                echo htmlspecialchars(
                    $data['doctor_name']
                );
                ?>

            </p>

        </div>

        <div class="col-md-6 text-end">

            <p class="detail-text">

                <strong>Date :</strong>

                <?php
                echo $data['date']->format('Y-m-d');
                ?>

            </p>

        </div>

    </div>

    <!-- MEDICINE TABLE -->

    <div class="table-responsive">

        <table class="table align-middle">

            <thead>

                <tr>

                    <th style="width:80px;">
                        No.
                    </th>

                    <th>
                        Medicine
                    </th>

                    <th style="width:150px;">
                        Quantity
                    </th>

                </tr>

            </thead>

            <tbody>

            <?php

            $count = 1;

            while(
                $row = sqlsrv_fetch_array(
                            $stmtItems,
                            SQLSRV_FETCH_ASSOC
                        )
            ){
            ?>

                <tr>

                    <td>
                        <?php echo $count++; ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $row['name']
                        );
                        ?>
                    </td>

                    <td>
                        <?php
                        echo $row['quantity'];
                        ?>
                    </td>

                </tr>

            <?php
            }
            ?>

            </tbody>

        </table>

    </div>

    <!-- SIGNATURE -->

    <div class="mt-5">

        <div class="signature-box">

            <hr>

            <p class="detail-text mb-0">

                Doctor Signature

            </p>

        </div>

    </div>

</div>

<!-- ================= ACTION BUTTONS ================= -->

<div class="mt-3 no-print">

    <button
        type="button"
        onclick="printPrescription()"
        class="btn btn-primary">

        Print

    </button>

    <a href="prescriptions.php"
       class="btn btn-secondary">

        Back

    </a>

</div>

<?php include 'includes/footer.php'; ?>

<!-- ================= PRINT SCRIPT ================= -->

<script>

function printPrescription(){

    window.print();

}

</script>