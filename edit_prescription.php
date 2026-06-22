<?php 
$pageTitle = "Edit Prescription";
include 'includes/sidebar.php';
include 'config.php';

$showSuccess = false;
$successMessage = "";

// ================= GET ID =================
if(!isset($_GET['id'])){
    echo "<div class='alert alert-danger'>Invalid request</div>";
    exit;
}

$id = intval($_GET['id']);

// ================= GET PRESCRIPTION =================
$sql = "SELECT p.*, pa.name AS patient_name, d.doctor_id, d.name AS doctor_name
        FROM Prescription p
        JOIN Patient pa ON p.patient_id = pa.patient_id
        JOIN Doctor d ON p.doctor_id = d.doctor_id
        WHERE p.prescription_id = ?";

$stmt = sqlsrv_query($conn, $sql, array($id));
$data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if(!$data){
    echo "<div class='alert alert-danger'>Prescription not found</div>";
    exit;
}

// ================= UPDATE =================
if(isset($_POST['update'])){

    $doctor_id = intval($_POST['doctor_id']);
    $medicines = $_POST['medicine_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    if($doctor_id <= 0){
        echo "<script>Swal.fire('Error','Invalid doctor','error');</script>";
    }
    elseif(count($medicines) == 0){
        echo "<script>Swal.fire('Error','No medicines selected','error');</script>";
    } 
    else {

        sqlsrv_begin_transaction($conn);

        try {

            // CHECK DOCTOR
            $checkDoctor = sqlsrv_query($conn,
                "SELECT doctor_id FROM Doctor WHERE doctor_id = ?",
                array($doctor_id)
            );

            if(!sqlsrv_fetch_array($checkDoctor)){
                throw new Exception("Doctor not found");
            }

            // UPDATE PRESCRIPTION
            $update = sqlsrv_query($conn,
                "UPDATE Prescription SET doctor_id = ? WHERE prescription_id = ?",
                array($doctor_id, $id)
            );

            if(!$update){
                throw new Exception("Doctor update failed");
            }

            // RESTORE OLD STOCK
            $oldItems = sqlsrv_query($conn,
                "SELECT medicine_id, quantity FROM Prescription_Items WHERE prescription_id = ?",
                array($id)
            );

            while($old = sqlsrv_fetch_array($oldItems, SQLSRV_FETCH_ASSOC)){
                sqlsrv_query($conn,
                    "UPDATE Inventory SET quantity = quantity + ? WHERE medicine_id = ?",
                    array($old['quantity'], $old['medicine_id'])
                );
            }

            // DELETE OLD ITEMS
            sqlsrv_query($conn,
                "DELETE FROM Prescription_Items WHERE prescription_id = ?",
                array($id)
            );

            // INSERT NEW ITEMS
            for($i=0; $i<count($medicines); $i++){

                $med_id = intval($medicines[$i]);
                $qty = intval($quantities[$i]);

                if($med_id <= 0 || $qty <= 0){
                    throw new Exception("Invalid medicine or quantity");
                }

                // 🔥 CHECK EXPIRY + STOCK
                $check = sqlsrv_query($conn,
                    "SELECT m.name, m.expiry_date, i.quantity
                     FROM Medicine m
                     JOIN Inventory i ON m.medicine_id = i.medicine_id
                     WHERE m.medicine_id = ?",
                    array($med_id)
                );

                $med = sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC);

                if(!$med){
                    throw new Exception("Medicine not found");
                }

                $name = $med['name'];

                // expiry check
                if($med['expiry_date']){
                    $exp = $med['expiry_date']->format('Y-m-d');
                    if(strtotime($exp) <= strtotime(date('Y-m-d'))){
                        throw new Exception("$name is expired");
                    }
                }

                if($med['quantity'] < $qty){
                    throw new Exception("$name - Not enough stock");
                }

                // INSERT
                sqlsrv_query($conn,
                    "INSERT INTO Prescription_Items (prescription_id, medicine_id, quantity)
                     VALUES (?, ?, ?)",
                    array($id, $med_id, $qty)
                );

                // UPDATE STOCK
                sqlsrv_query($conn,
                    "UPDATE Inventory SET quantity = quantity - ? WHERE medicine_id = ?",
                    array($qty, $med_id)
                );
            }

            sqlsrv_commit($conn);

            $showSuccess = true;
            $successMessage = "Prescription updated successfully";

        } catch(Exception $e){

            sqlsrv_rollback($conn);

            echo "<script>
            Swal.fire('Error','".$e->getMessage()."','error');
            </script>";
        }
    }
}

// ================= LOAD ITEMS =================
$items = sqlsrv_query($conn,
    "SELECT * FROM Prescription_Items WHERE prescription_id = ?",
    array($id)
);
?>

<div class="form-wrapper">

<div class="form-card">

<div class="form-title-box">

    <h3>
        Edit Prescription
    </h3>

</div>

<p class="mb-3">

    <strong>Patient :</strong>

    <?php echo htmlspecialchars($data['patient_name']); ?>

</p>

<form method="POST">

    <div class="mb-3">

        <label class="form-label">
            Doctor
        </label>

        <select
            name="doctor_id"
            class="form-select"
            required>

            <?php

            $doctors =
            sqlsrv_query(
                $conn,
                "SELECT * FROM Doctor ORDER BY name"
            );

            while(
                $d = sqlsrv_fetch_array(
                        $doctors,
                        SQLSRV_FETCH_ASSOC
                     )
            ){

                $selected =
                ($d['doctor_id']
                == $data['doctor_id'])
                ? "selected"
                : "";

                echo "
                <option
                    value='{$d['doctor_id']}'
                    $selected>

                    ".htmlspecialchars($d['name'])."

                </option>";
            }

            ?>

        </select>

    </div>

    <label class="form-label">
        Medicines
    </label>

    <div id="medicineBody">

        <?php
        while(
            $row = sqlsrv_fetch_array(
                        $items,
                        SQLSRV_FETCH_ASSOC
                    )
        ){
        ?>

        <div class="row mb-3 medicine-row">

            <div class="col-md-6">

                <select
                    name="medicine_id[]"
                    class="form-select"
                    required>

                    <?php

                    $meds =
                    sqlsrv_query(
                        $conn,
                        "
                        SELECT
                            m.medicine_id,
                            m.name,
                            m.expiry_date,
                            i.quantity
                        FROM Medicine m
                        INNER JOIN Inventory i
                            ON m.medicine_id = i.medicine_id
                        "
                    );

                    while(
                        $m = sqlsrv_fetch_array(
                                $meds,
                                SQLSRV_FETCH_ASSOC
                            )
                    ){

                        $med_id =
                        $m['medicine_id'];

                        $name =
                        htmlspecialchars(
                            $m['name']
                        );

                        $stock =
                        (int)$m['quantity'];

                        $isExpired =
                        false;

                        if(
                            $m['expiry_date']
                        ){

                            $exp =
                            $m['expiry_date']
                            ->format('Y-m-d');

                            if(
                                strtotime($exp)
                                <= strtotime(
                                    date('Y-m-d')
                                )
                            ){
                                $isExpired = true;
                            }
                        }

                        $disabled =
                        (
                            ($isExpired || $stock < 10)
                            &&
                            $med_id
                            != $row['medicine_id']
                        )
                        ? "disabled"
                        : "";

                        $selected =
                        (
                            $med_id
                            == $row['medicine_id']
                        )
                        ? "selected"
                        : "";

                        if($isExpired){

                            $label =
                            "$name (Expired)";

                        }elseif($stock < 10){

                            $label =
                            "$name (Low: $stock)";

                        }else{

                            $label =
                            "$name ($stock)";
                        }

                        echo "
                        <option
                            value='$med_id'
                            $selected
                            $disabled>

                            $label

                        </option>";
                    }

                    ?>

                </select>

            </div>

            <div class="col-md-4">

                <input
                    type="number"
                    name="quantity[]"
                    value="<?php echo $row['quantity']; ?>"
                    class="form-control"
                    min="1"
                    required>

            </div>

            <div class="col-md-2">

                <button
                    type="button"
                    class="btn btn-danger w-100"
                    onclick="removeRow(this)">

                    Remove

                </button>

            </div>

        </div>

        <?php } ?>

    </div>

    <button
        type="button"
        class="btn btn-primary mb-3"
        onclick="addRow()">

        + Add Medicine

    </button>

    <div class="d-flex gap-2">

        <button
            type="submit"
            name="update"
            class="btn btn-primary">

            Update Prescription

        </button>

        <a href="prescriptions.php"
           class="btn btn-secondary">

            Cancel

        </a>

    </div>

</form>


</div>

</div>


</div>
</div>

<?php include 'includes/footer.php'; ?>

<?php if($showSuccess): ?>
<script>
Swal.fire({
    title: 'Updated!',
    text: '<?php echo $successMessage; ?>',
    icon: 'success'
}).then(() => {
    window.location.href = 'prescriptions.php';
});
</script>
<?php endif; ?>

<script>
function addRow(){
    let container = document.getElementById("medicineBody");
    let row = container.children[0].cloneNode(true);

    row.querySelectorAll("select").forEach(e => e.selectedIndex = 0);
    row.querySelectorAll("input").forEach(e => e.value = "");

    container.appendChild(row);
}

function removeRow(btn){
    let rows = document.querySelectorAll("#medicineBody .row");

    if(rows.length > 1){
        btn.closest(".row").remove();
    } else {
        Swal.fire("Error","At least one medicine required","error");
    }
}
</script>