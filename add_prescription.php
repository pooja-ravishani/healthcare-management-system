<?php

$pageTitle = "Add Prescription";

include 'includes/sidebar.php';
include 'config.php';

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- PAGE TOP -->

<div class="page-top">

<h2 class="page-heading">
    Add Prescription
</h2>


</div>

<!-- FORM -->

<div class="form-wrapper">

<div class="form-card">

<div class="form-title-box">

    <h3>
        Add Prescription
    </h3>

</div>

<form method="POST"
      action="save_prescription.php">

    <!-- Patient -->

<div class="mb-3">

    <label class="form-label">
        Patient
    </label>

    <select name="patient_id"
            class="form-select"
            required>

        <option value="">
            Select Patient
        </option>

        <?php

        $p = sqlsrv_query(
                $conn,
                "SELECT * FROM Patient ORDER BY patient_id DESC"
             );

        while(
            $row = sqlsrv_fetch_array(
                        $p,
                        SQLSRV_FETCH_ASSOC
                    )
        ){

            echo "

            <option value='".$row['patient_id']."'>

                PID".$row['patient_id']."
                - ".
                htmlspecialchars($row['name'])."

            </option>

            ";
        }

        ?>

    </select>

</div>

    <!-- Doctor -->

<div class="mb-3">

    <label class="form-label">
        Doctor
    </label>

    <select name="doctor_id"
            class="form-select"
            required>

        <option value="">
            Select Doctor
        </option>

        <?php

        $d = sqlsrv_query(
                $conn,
                "SELECT * FROM Doctor ORDER BY doctor_id DESC"
             );

        while(
            $row = sqlsrv_fetch_array(
                        $d,
                        SQLSRV_FETCH_ASSOC
                    )
        ){

            echo "

            <option value='".$row['doctor_id']."'>

                DID".$row['doctor_id']."
                - ".
                htmlspecialchars($row['name'])."

            </option>

            ";
        }

        ?>

    </select>

</div>

    <!-- Medicines -->

    <label class="form-label">
        Medicines
    </label>

    <div id="medicineBody">

        <div class="row mb-3 medicine-row">

            <div class="col-md-6">

                <select
                    name="medicine_id[]"
                    class="form-select medicine-select"
                    required>

                    <option value="">
                        Select Medicine
                    </option>

                    <?php

                    $m = sqlsrv_query(
                        $conn,
                        "
                        SELECT
                            m.medicine_id,
                            m.name,
                            dbo.fn_total_stock(m.medicine_id) AS stock,
                            dbo.fn_is_expired(m.medicine_id) AS expired
                        FROM Medicine m
                        ORDER BY m.name ASC
                        "
                    );

                    while(
                        $row = sqlsrv_fetch_array(
                                    $m,
                                    SQLSRV_FETCH_ASSOC
                                )
                    ){

                        $med_id =
                        $row['medicine_id'];

                        $name =
                        htmlspecialchars(
                            $row['name']
                        );

                        $stock =
                        (int)$row['stock'];

                        $expired =
                        (int)$row['expired'];

                        $disabled =
                        ($expired == 1 || $stock <= 0)
                        ? "disabled"
                        : "";

                        if($expired){

                            $label =
                            "$name (Expired)";

                        }
                        elseif($stock <= 0){

                            $label =
                            "$name (Out of Stock)";

                        }
                        elseif($stock <= 10){

                            $label =
                            "$name (Low: $stock)";

                        }
                        else{

                            $label =
                            "$name ($stock)";
                        }

                        echo "
                        <option
                            value='$med_id'
                            data-stock='$stock'
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
                    class="form-control qty-input"
                    min="1"
                    placeholder="Quantity"
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

    </div>

    <button
        type="button"
        class="btn btn-primary mb-3"
        onclick="addRow()">

        + Add Medicine

    </button>

    <div class="d-flex gap-2 mt-3">

        <button
            type="submit"
            class="btn btn-primary">

            Save Prescription

        </button>

        <a href="prescriptions.php"
           class="btn btn-secondary">

            Cancel

        </a>

    </div>

</form>


</div>

</div>

<?php include 'includes/footer.php'; ?>

<script>

function addRow(){

    let container =
    document.getElementById(
        "medicineBody"
    );

    let row =
    container.children[0]
    .cloneNode(true);

    row.querySelectorAll(
        "select"
    ).forEach(
        e => e.selectedIndex = 0
    );

    row.querySelectorAll(
        "input"
    ).forEach(
        e => e.value = ""
    );

    container.appendChild(row);
}

function removeRow(btn){

    let rows =
    document.querySelectorAll(
        "#medicineBody .medicine-row"
    );

    if(rows.length > 1){

        btn.closest(
            ".medicine-row"
        ).remove();

    }else{

        Swal.fire(
            "Error",
            "At least one medicine is required",
            "error"
        );
    }
}

document.addEventListener(
    "change",
    function(e){

        if(
            e.target.classList.contains(
                "medicine-select"
            )
        ){

            let selected =
            e.target.options[
                e.target.selectedIndex
            ];

            let stock =
            parseInt(
                selected.getAttribute(
                    "data-stock"
                ) || 0
            );

            let qtyInput =
            e.target.closest(
                ".medicine-row"
            ).querySelector(
                ".qty-input"
            );

            if(qtyInput){

                qtyInput.max = stock;
            }
        }
    }
);

document.addEventListener(
    "input",
    function(e){

        if(
            e.target.classList.contains(
                "qty-input"
            )
        ){

            let max =
            parseInt(
                e.target.max || 0
            );

            if(
                max > 0 &&
                e.target.value > max
            ){

                e.target.value = max;

                Swal.fire({

                    icon:"warning",

                    title:"Stock Limit",

                    text:"Cannot exceed available stock"

                });
            }
        }
    }
);

</script>
