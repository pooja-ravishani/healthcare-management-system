<?php
$pageTitle = "Doctors";
include 'includes/sidebar.php';
include 'config.php';
?>

<!-- ================= PAGE TOP ================= -->

<div class="page-top">

    <h2 class="page-heading">
        Doctor List
    </h2>

    <a href="add_doctor.php" class="btn btn-primary">
        + Add Doctor
    </a>

</div>

<!-- ================= TABLE CARD ================= -->

<div class="table-card">

<div class="table-responsive">

<table class="table align-middle">

    <thead>

        <tr>

            <th>ID</th>

            <th>Name</th>

            <th>Specialization</th>

            <th style="width:220px;">
                Actions
            </th>

        </tr>

    </thead>

    <tbody>

<?php

$sql = "
SELECT *
FROM Doctor
ORDER BY doctor_id DESC
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

        $id = $row['doctor_id'];

        $name = htmlspecialchars(
                    $row['name']
                );

        $specialization = htmlspecialchars(
                            $row['specialization']
                          );
?>

<tr id="row_<?php echo $id; ?>">

    <td>
        <?php echo $id; ?>
    </td>

    <td>
        <?php echo $name; ?>
    </td>

    <td>

        <span class="badge bg-info">

            <?php echo $specialization; ?>

        </span>

    </td>

    <td>

        <div class="action-buttons">

            <a href="edit_doctor.php?id=<?php echo $id; ?>"
               class="btn btn-primary btn-sm">

                Edit

            </a>

            <button
                class="btn btn-danger btn-sm"
                onclick="deleteDoctor(<?php echo $id; ?>)">

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

        <td colspan='4'
            class='text-center'>

            No doctors found

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

function deleteDoctor(id){

    Swal.fire({

        title: 'Delete Doctor?',

        text: 'This action cannot be undone',

        icon: 'warning',

        showCancelButton: true,

        confirmButtonColor: '#2563EB',

        cancelButtonColor: '#dc3545',

        confirmButtonText: 'Delete'

    }).then((result) => {

        if(result.isConfirmed){

            fetch(
                "delete_doctor.php?id=" + id
            )

            .then(res => res.json())

            .then(data => {

                if(data.status === "success"){

                    const row =
                    document.getElementById(
                        "row_" + id
                    );

                    if(row){
                        row.remove();
                    }

                    Swal.fire({
                        icon:'success',
                        title:'Deleted!',
                        text:data.message
                    });

                }else{

                    Swal.fire({
                        icon:'error',
                        title:'Error',
                        text:data.message
                    });
                }

            })

            .catch(() => {

                Swal.fire({
                    icon:'error',
                    title:'Error',
                    text:'Server error'
                });

            });

        }

    });

}

</script>