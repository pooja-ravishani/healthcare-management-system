<?php

$pageTitle = "My Profile";

include 'includes/sidebar.php';
include 'auth_check.php';
include 'config.php';

$id = $_SESSION['user_id'];

$sql = "
SELECT
    name,
    email,
    role,
    profile_img
FROM Users
WHERE user_id = ?
";

$stmt = sqlsrv_query($conn, $sql, array($id));

if($stmt === false){

    $sql = "
    SELECT
        name,
        email,
        role
    FROM Users
    WHERE user_id = ?
    ";

    $stmt = sqlsrv_query($conn, $sql, array($id));
}

$user = sqlsrv_fetch_array(
            $stmt,
            SQLSRV_FETCH_ASSOC
        );

$showSuccess = false;

/* ================= UPDATE ================= */

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $name = trim($_POST['name']);

    $password = $_POST['password'] ?? "";

    /* IMAGE UPLOAD */

    if(
        isset($_FILES['profile_img']) &&
        $_FILES['profile_img']['error'] == 0
    ){

        if(!is_dir("uploads")){
            mkdir("uploads");
        }

        $fileName =
        time() . "_" .
        basename($_FILES['profile_img']['name']);

        $target =
        "uploads/" . $fileName;

        move_uploaded_file(
            $_FILES['profile_img']['tmp_name'],
            $target
        );

        @sqlsrv_query(
            $conn,
            "UPDATE Users
             SET profile_img = ?
             WHERE user_id = ?",
            array(
                $fileName,
                $id
            )
        );

        $user['profile_img'] = $fileName;
    }

    /* PASSWORD UPDATE */

    if(!empty($password)){

        $hashed =
        password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        sqlsrv_query(
            $conn,
            "UPDATE Users
             SET name = ?,
                 password = ?
             WHERE user_id = ?",
            array(
                $name,
                $hashed,
                $id
            )
        );

    }else{

        sqlsrv_query(
            $conn,
            "UPDATE Users
             SET name = ?
             WHERE user_id = ?",
            array(
                $name,
                $id
            )
        );
    }

    $user['name'] = $name;

    $showSuccess = true;
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- PAGE HEADER -->

<div class="page-top">

    <h2 class="page-heading">
        My Profile
    </h2>

</div>

<!-- FORM -->

<div class="form-wrapper">

<div class="form-card">

<form
    method="POST"
    enctype="multipart/form-data">

    <?php

    $imgPath =
    "https://via.placeholder.com/120";

    if(
        isset($user['profile_img']) &&
        !empty($user['profile_img'])
    ){

        $imgPath =
        "uploads/" .
        $user['profile_img'];
    }

    ?>

    <!-- PROFILE IMAGE -->

    <div class="text-center mb-4">

        <img
            id="preview"
            src="<?php echo $imgPath; ?>"
            class="profile-preview-img">

        <input
            type="file"
            name="profile_img"
            class="form-control mt-3"
            onchange="previewImage(event)">

    </div>

    <!-- NAME -->

    <div class="mb-3">

        <label class="form-label">
            Name
        </label>

        <input
            type="text"
            name="name"
            class="form-control"
            value="<?php echo htmlspecialchars($user['name']); ?>"
            required>

    </div>

    <!-- EMAIL -->

    <div class="mb-3">

        <label class="form-label">
            Email
        </label>

        <input
            type="email"
            class="form-control"
            value="<?php echo htmlspecialchars($user['email']); ?>"
            disabled>

    </div>

    <!-- ROLE -->

    <div class="mb-3">

        <label class="form-label">
            Role
        </label>

        <input
            type="text"
            class="form-control"
            value="<?php echo htmlspecialchars($user['role']); ?>"
            disabled>

    </div>

    <!-- PASSWORD -->

    <div class="mb-3">

        <label class="form-label">
            New Password
        </label>

        <input
            type="password"
            name="password"
            class="form-control"
            placeholder="Leave blank if no change">

    </div>

    <!-- BUTTONS -->

    <div class="form-actions">

        <button
            type="submit"
            class="btn btn-primary">

            Update Profile

        </button>

    </div>

</form>

</div>

</div>

<?php include 'includes/footer.php'; ?>

<script>

function previewImage(event){

    const reader =
    new FileReader();

    reader.onload =
    function(){

        document
        .getElementById("preview")
        .src = reader.result;
    };

    reader.readAsDataURL(
        event.target.files[0]
    );
}

</script>

<?php if($showSuccess): ?>

<script>

Swal.fire({

    title: 'Success!',

    text: 'Profile updated successfully',

    icon: 'success',

    confirmButtonColor: '#2563EB'

});

</script>

<?php endif; ?>