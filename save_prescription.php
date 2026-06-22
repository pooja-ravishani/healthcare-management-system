<?php
include 'config.php';

// ================= METHOD CHECK =================
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header("Location: add_prescription.php?error=Invalid request");
    exit;
}

// ================= GET DATA =================
$patient_id = isset($_POST['patient_id']) ? intval($_POST['patient_id']) : 0;
$doctor_id  = isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : 0;

$medicines  = $_POST['medicine_id'] ?? [];
$quantities = $_POST['quantity'] ?? [];

// ensure arrays
if(!is_array($medicines)) $medicines = [$medicines];
if(!is_array($quantities)) $quantities = [$quantities];

// ================= VALIDATION =================
if($patient_id <= 0 || $doctor_id <= 0){
    header("Location: add_prescription.php?error=Select patient and doctor");
    exit;
}

if(empty($medicines) || empty($quantities)){
    header("Location: add_prescription.php?error=No medicines selected");
    exit;
}

if(count($medicines) !== count($quantities)){
    header("Location: add_prescription.php?error=Data mismatch");
    exit;
}

// duplicate check
if(count($medicines) !== count(array_unique($medicines))){
    header("Location: add_prescription.php?error=Duplicate medicines not allowed");
    exit;
}

// ================= START TRANSACTION =================
sqlsrv_begin_transaction($conn);

try {

    // ================= INSERT PRESCRIPTION =================
    $sql = "INSERT INTO Prescription (patient_id, doctor_id, date)
            OUTPUT INSERTED.prescription_id
            VALUES (?, ?, GETDATE())";

    $stmt = sqlsrv_query($conn, $sql, [$patient_id, $doctor_id]);

    if(!$stmt){
        throw new Exception("Failed to create prescription");
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $prescription_id = intval($row['prescription_id']);

    // ================= LOOP =================
    for($i = 0; $i < count($medicines); $i++){

        $med_id = intval($medicines[$i]);
        $qty    = intval($quantities[$i]);

        if($med_id <= 0 || $qty <= 0){
            throw new Exception("Invalid medicine or quantity");
        }

        // ================= MEDICINE CHECK =================
        $check = sqlsrv_query($conn,
            "SELECT name, expiry_date FROM Medicine WHERE medicine_id = ?",
            [$med_id]
        );

        $med = sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC);

        if(!$med){
            throw new Exception("Medicine not found");
        }

        $medName = $med['name'];

        // expiry check
        if($med['expiry_date']){
            $expiry = $med['expiry_date']->format('Y-m-d');

            if(strtotime($expiry) <= strtotime(date('Y-m-d'))){
                throw new Exception("$medName is expired");
            }
        }

        // ================= STOCK CHECK =================
        $stockCheck = sqlsrv_query($conn,
            "SELECT quantity FROM Inventory WHERE medicine_id = ?",
            [$med_id]
        );

        $stock = sqlsrv_fetch_array($stockCheck, SQLSRV_FETCH_ASSOC);

        if(!$stock){
            throw new Exception("Inventory not found for $medName");
        }

        if($stock['quantity'] < $qty){
            throw new Exception("$medName - Not enough stock");
        }

        // ================= INSERT ITEM =================
        $insertItem = sqlsrv_query($conn,
            "INSERT INTO Prescription_Items (prescription_id, medicine_id, quantity)
             VALUES (?, ?, ?)",
            [$prescription_id, $med_id, $qty]
        );

        if(!$insertItem){
            throw new Exception("Failed to save prescription item");
        }

        // ================= UPDATE STOCK =================
        $updateStock = sqlsrv_query($conn,
            "UPDATE Inventory SET quantity = quantity - ? WHERE medicine_id = ?",
            [$qty, $med_id]
        );

        if(!$updateStock){
            throw new Exception("Failed to update stock");
        }
    }

    // ================= COMMIT =================
    sqlsrv_commit($conn);

    header("Location: prescriptions.php?success=1");
    exit;

} catch(Exception $e){

    sqlsrv_rollback($conn);

    header("Location: add_prescription.php?error=" . urlencode($e->getMessage()));
    exit;
}
?>