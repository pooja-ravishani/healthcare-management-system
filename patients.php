<?php 
$pageTitle = "Patients";
include 'includes/sidebar.php';
include 'config.php';
?>

<!-- ================= PAGE TOP ================= -->

<div class="page-top">

    <h2 class="page-heading">
        Patient List
    </h2>

    <a href="add_patient.php" class="btn btn-primary">
        + Add Patient
    </a>

</div>

<!-- ================= TABLE CARD ================= -->

<div class="table-card">

    <div class="table-responsive">

        <table class="table patient-table align-middle">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Contact</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php

            $sql = "SELECT * FROM Patient ORDER BY patient_id DESC";
            $stmt = sqlsrv_query($conn, $sql);

            if($stmt){

                while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){

            ?>

                <tr id="row_<?php echo $row['patient_id']; ?>">

                    <td>
                        <?php echo $row['patient_id']; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['name']); ?>
                    </td>

                    <td>
                        <?php echo $row['age']; ?>
                    </td>

                    <td>

                        <?php
                        $gender = strtolower($row['gender']);
                        ?>

                        <span class="badge rounded-pill px-3 py-2
                        <?php echo ($gender == 'male') ? 'bg-info' : 'bg-success'; ?>">

                            <?php echo htmlspecialchars($row['gender']); ?>

                        </span>

                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['contact']); ?>
                    </td>

                    <td>

                        <div class="action-buttons">

                            <a href="edit_patient.php?id=<?php echo $row['patient_id']; ?>"
                               class="btn btn-primary btn-sm">
                                Edit
                            </a>

                            <button class="btn btn-danger btn-sm"
                                    onclick="deletePatient(<?php echo $row['patient_id']; ?>)">
                                Delete
                            </button>

                            <a href="patient_history.php?id=<?php echo $row['patient_id']; ?>"
                               class="btn btn-secondary btn-sm">
                                History
                            </a>

                        </div>

                    </td>

                </tr>

            <?php

                }

            }else{

                echo "
                <tr>
                    <td colspan='6' class='text-center py-4'>
                        No patient records found
                    </td>
                </tr>";
            }

            ?>

            </tbody>

        </table>

    </div>

</div>

<?php include 'includes/footer.php'; ?>