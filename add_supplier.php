<?php

$pageTitle = "Add Supplier";

include 'includes/sidebar.php';
include 'config.php';

?>

<div class="form-wrapper">

<div class="form-card">

```
<div class="form-title-box">

    <h3>
        Add Supplier
    </h3>

</div>

<form method="POST"
      action="save_supplier.php">

    <div class="row">

        <div class="col-md-12 mb-3">

            <label class="form-label">
                Supplier Name
            </label>

            <input
                type="text"
                name="name"
                class="form-control"
                required>

        </div>

        <div class="col-md-12 mb-3">

            <label class="form-label">
                Contact Number
            </label>

            <input
                type="text"
                name="contact"
                class="form-control"
                required>

        </div>

    </div>

    <div class="d-flex gap-2 mt-3">

        <button
            type="submit"
            class="btn btn-primary">

            Save Supplier

        </button>

        <a href="suppliers.php"
           class="btn btn-secondary">

            Cancel

        </a>

    </div>

</form>
```

</div>

</div>

<?php include 'includes/footer.php'; ?>
