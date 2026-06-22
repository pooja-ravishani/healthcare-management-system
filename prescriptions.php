<?php

$pageTitle = "Prescriptions";

include 'includes/sidebar.php';
include 'config.php';

header("Cache-Control: no-cache, must-revalidate");

?>

<?php if(isset($_GET['success'])): ?>

<script>

Swal.fire({

    title:'Success!',
    text:'<?php echo htmlspecialchars($_GET["success"]); ?>',
    icon:'success',
    timer:1500,
    showConfirmButton:false

});

</script>

<?php endif; ?>

<?php if(isset($_GET['error'])): ?>

<script>

Swal.fire({

    title:'Error!',
    text:'<?php echo htmlspecialchars($_GET["error"]); ?>',
    icon:'error'

});

</script>

<?php endif; ?>

<!-- PAGE TOP -->

<div class="page-top">


<h2 class="page-heading">
    Prescription List
</h2>

<a href="add_prescription.php"
   class="btn btn-primary">

    + New Prescription

</a>


</div>

<!-- TABLE CARD -->

<div class="table-card">

<div class="table-responsive">

<table class="table align-middle">


<thead>

    <tr>

        <th>ID</th>

        <th>Patient</th>

        <th>Doctor</th>

        <th>Date</th>

        <th>Medicines</th>

        <th style="width:280px;">
            Actions
        </th>

    </tr>

</thead>

<tbody>

<?php

$sql = "
SELECT
    p.prescription_id,
    p.date,
    pt.name AS patient_name,
    d.name AS doctor_name
FROM Prescription p
INNER JOIN Patient pt
    ON p.patient_id = pt.patient_id
INNER JOIN Doctor d
    ON p.doctor_id = d.doctor_id
ORDER BY p.prescription_id DESC
";

$stmt = sqlsrv_query($conn, $sql);

$hasData = false;

if($stmt){

    while(
        $row = sqlsrv_fetch_array(
                    $stmt,
                    SQLSRV_FETCH_ASSOC
                )
    ){

        $hasData = true;

        $pid = $row['prescription_id'];

        $patient =
        htmlspecialchars(
            $row['patient_name']
        );

        $doctor =
        htmlspecialchars(
            $row['doctor_name']
        );

        $date =
        $row['date']
        ? $row['date']->format('Y-m-d')
        : '-';

?>

<tr id="row_<?php echo $pid; ?>">


<td>
    <?php echo $pid; ?>
</td>

<td>
    <?php echo $patient; ?>
</td>

<td>
    <?php echo $doctor; ?>
</td>

<td>
    <?php echo $date; ?>
</td>

<td>


<?php

$sql2 = "
SELECT
    m.name,
    pi.quantity
FROM Prescription_Items pi
INNER JOIN Medicine m
    ON pi.medicine_id = m.medicine_id
WHERE pi.prescription_id = ?
";

$stmt2 = sqlsrv_query(
            $conn,
            $sql2,
            array($pid)
         );

if($stmt2){

    while(
        $med = sqlsrv_fetch_array(
                    $stmt2,
                    SQLSRV_FETCH_ASSOC
                )
    ){

?>


    <div class="mb-1">

        <span class="badge bg-success">

            <?php
            echo htmlspecialchars(
                $med['name']
            );
            ?>

        </span>

        x <?php echo $med['quantity']; ?>

    </div>


<?php

    }
}

?>


</td>

<td>

    <div class="action-buttons">

        <a href="view_prescription.php?id=<?php echo $pid; ?>"
           class="btn btn-primary btn-sm">

            View

        </a>

        <a href="edit_prescription.php?id=<?php echo $pid; ?>"
           class="btn btn-secondary btn-sm">

            Edit

        </a>

        <button
            class="btn btn-danger btn-sm"
            onclick="deletePrescription(<?php echo $pid; ?>)">

            Delete

        </button>

    </div>

</td>


</tr>

<?php

    }
}

if(!$hasData){

    echo "
    <tr>

        <td colspan='6'
            class='text-center'>

            No prescriptions found

        </td>

    </tr>";
}

?>


</tbody>


</table>

</div>

</div>

<?php include 'includes/footer.php'; ?>

<script>

function deletePrescription(id){

    Swal.fire({

        title:'Delete Prescription?',

        text:'This action cannot be undone',

        icon:'warning',

        showCancelButton:true,

        confirmButtonColor:'#2563EB',

        cancelButtonColor:'#dc3545',

        confirmButtonText:'Delete'

    }).then((result)=>{

        if(result.isConfirmed){

            window.location =
            'delete_prescription.php?id=' + id;
        }

    });

}

</script>
