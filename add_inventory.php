<?php

$pageTitle = "Add Inventory";

include 'includes/sidebar.php';
include 'config.php';

?>

<div class="form-wrapper">

    <div class="form-card">

        <div class="form-title-box">

            <h3>
                Add Inventory
            </h3>

        </div>

        <form action="save_inventory.php"
              method="POST">

            <div class="row">

                <!-- Medicine -->

                <div class="col-md-12 mb-3">

                    <label class="form-label">
                        Medicine
                    </label>

                    <select name="medicine_id"
                            class="form-select"
                            required>

                        <option value="">
                            Select Medicine
                        </option>

                        <?php

                        $sql = "
                        SELECT *
                        FROM Medicine
                        ORDER BY name ASC
                        ";

                        $stmt = sqlsrv_query(
                                    $conn,
                                    $sql
                                );

                        while(
                            $row = sqlsrv_fetch_array(
                                        $stmt,
                                        SQLSRV_FETCH_ASSOC
                                    )
                        ){

                            echo "
                            <option value='".$row['medicine_id']."'>
                                ".htmlspecialchars($row['name'])."
                            </option>
                            ";
                        }

                        ?>

                    </select>

                </div>

                <!-- Quantity -->

                <div class="col-md-12 mb-3">

                    <label class="form-label">
                        Quantity
                    </label>

                    <input type="number"
                           name="quantity"
                           class="form-control"
                           min="1"
                           required>

                </div>

            </div>

            <div class="d-flex gap-2 mt-3">

                <button type="submit"
                        class="btn btn-primary">

                    Save Inventory

                </button>

                <a href="inventory.php"
                   class="btn btn-secondary">

                    Cancel

                </a>

            </div>

        </form>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
