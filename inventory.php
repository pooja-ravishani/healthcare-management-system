<?php

$pageTitle = "Inventory";

include 'includes/sidebar.php';
include 'config.php';

header("Cache-Control: no-cache, must-revalidate");

?>

<!-- PAGE HEADER -->

<div class="page-top">

<h2 class="page-heading">
    Inventory
</h2>

<a href="add_inventory.php"
   class="btn btn-primary">

    + Add Inventory

</a>


</div>

<!-- INVENTORY TABLE -->

<div class="table-card">

<div class="table-responsive">

<table class="table align-middle">


<thead>

    <tr>

        <th>Medicine</th>

        <th>Stock</th>

        <th>Status</th>

        <th style="width:220px;">
            Actions
        </th>

    </tr>

</thead>

<tbody>


<?php

/* Retrieve inventory records */

$sql = "
SELECT
    i.inventory_id,
    i.medicine_id,
    i.quantity,
    m.name
FROM Inventory i
INNER JOIN Medicine m
    ON i.medicine_id = m.medicine_id
ORDER BY m.name ASC
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

        $id = $row['inventory_id'];

        $stock = (int)$row['quantity'];

?>

<tr id="row_<?php echo $id; ?>">

<td>

    <?php echo htmlspecialchars($row['name']); ?>

</td>

<td>

    <strong>

        <?php echo $stock; ?>

    </strong>

</td>

<td>

    <?php

    if($stock <= 10){

        echo "
        <span class='badge bg-danger'>
            Low
        </span>";

    }
    elseif($stock <= 30){

        echo "
        <span class='badge bg-warning text-dark'>
            Medium
        </span>";

    }
    else{

        echo "
        <span class='badge bg-success'>
            OK
        </span>";
    }

    ?>

</td>

<td>

    <div class="action-buttons">

        <a href="edit_inventory.php?id=<?php echo $id; ?>"
           class="btn btn-primary btn-sm">

            Update

        </a>

        <button
            class="btn btn-danger btn-sm"
            onclick="deleteInventory(<?php echo $id; ?>)">

            Delete

        </button>

    </div>

</td>


</tr>

<?php

    }
}

if(!$hasData){

?>

<tr>


<td colspan="4"
    class="text-center">

    No inventory data found

</td>


</tr>

<?php

}

?>

</tbody>


</table>

</div>

</div>

<?php include 'includes/footer.php'; ?>

<script>

/* Delete inventory record */

function deleteInventory(id){

    Swal.fire({

        title:'Delete Inventory?',

        text:'This action cannot be undone',

        icon:'warning',

        showCancelButton:true,

        confirmButtonColor:'#2563EB',

        cancelButtonColor:'#dc3545',

        confirmButtonText:'Delete'

    }).then((result)=>{

        if(result.isConfirmed){

            window.location =
            'delete_inventory.php?id=' + id;
        }

    });

}

</script>
