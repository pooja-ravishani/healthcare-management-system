<?php
include 'config.php';

echo "DB Connected Successfully!";
?>

<form method="POST" action="prescription.php">

    Patient ID: <input type="number" name="patient_id" value="1"><br><br>
    Doctor ID: <input type="number" name="doctor_id" value="1"><br><br>

    Medicine ID: <input type="number" name="medicine_id[]" value="1"><br><br>
    Quantity: <input type="number" name="quantity[]" value="2"><br><br>

    <button type="submit">Submit</button>

</form>