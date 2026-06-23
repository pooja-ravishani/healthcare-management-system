<?php
$pageTitle = "Medicines";
include 'includes/sidebar.php';
include 'config.php';
?>

<!-- PAGE HEADER -->

<div class="page-top">

<h2 class="page-heading">
    Medicine List
</h2>

<a href="add_medicine.php" class="btn btn-primary">
    + Add Medicine
</a>


</div>

<!-- MEDICINE TABLE -->

<div class="table-card">

<div class="table-responsive">

<table class="table align-middle">


<thead>

    <tr>

        <th>ID</th>
        <th>Name</th>
        <th>Price (Rs.)</th>
        <th>Stock</th>
        <th>Expiry</th>
        <th style="width:220px;">Actions</th>

    </tr>

</thead>

<tbody>


<?php

/* Retrieve medicines with stock and expiry status */

$sql = "
SELECT
    m.*,
    dbo.fn_total_stock(m.medicine_id) AS total_stock,
    dbo.fn_is_expired(m.medicine_id) AS is_expired
FROM Medicine m
ORDER BY m.medicine_id DESC
";

$stmt = sqlsrv_query($conn, $sql);

$hasData = false;

if($stmt){

    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){

        $hasData = true;

        $id = $row['medicine_id'];
        $name = htmlspecialchars($row['name']);
        $price = number_format($row['price'], 2);

        $stock = (int)$row['total_stock'];
        $expired = (int)$row['is_expired'];

        $expiry = $row['expiry_date']
                    ? $row['expiry_date']->format('Y-m-d')
                    : '-';
?>

<tr id="row_<?php echo $id; ?>">

<td><?php echo $id; ?></td>

<td><?php echo $name; ?></td>

<td>Rs. <?php echo $price; ?></td>

<!-- Stock Status -->
<td>

    <?php

    if($stock <= 0){

        echo "<span class='badge bg-danger'>Out</span>";

    }elseif($stock <= 10){

        echo "<span class='badge bg-warning text-dark'>
                Low ($stock)
              </span>";

    }else{

        echo "<span class='badge bg-success'>
                $stock
              </span>";
    }

    ?>

</td>

<!-- Expiry Status -->
<td>

    <?php

    if($expired == 1){

        echo "<span class='badge bg-danger'>
                Expired
              </span>";

    }else{

        echo "<span class='badge bg-success'>
                Valid
              </span>";
    }

    ?>

    <br>

    <small class="text-muted">

        <?php echo $expiry; ?>

    </small>

</td>

<!-- Actions -->
<td>

    <div class="action-buttons">

        <a href="edit_medicine.php?id=<?php echo $id; ?>"
           class="btn btn-primary btn-sm">

            Edit

        </a>

        <button class="btn btn-danger btn-sm"
                onclick="deleteMedicine(<?php echo $id; ?>)">

            Delete

        </button>

    </div>

</td>


</tr>

<?php

    }
}

/* Display message if no medicines exist */

if(!$hasData){

    echo "
    <tr>

        <td colspan='6' class='text-center'>

            No medicines found

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

/* Delete medicine record */

function deleteMedicine(id){

    Swal.fire({

        title: 'Delete Medicine?',
        text: 'This action cannot be undone',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563EB',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Delete'

    }).then((result) => {

        if(result.isConfirmed){

            fetch("delete_medicine.php?id=" + id)

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

            });
        }
    });
}

</script>
