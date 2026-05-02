<?php
require_once "utils/GoBack.php";
require_once "utils/AppTime.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Category</title>

    <base href="<?= $web_root ?>">

    <meta name="author" content="Manu_Angate">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <title>Category</title>
</head>

<body>
    <main class="container" style="padding-top: 80px; padding-bottom: 100px;">
        <!-- HEADER -->
        <?php
        $page_title = "Category";
        $hide_back_button = true;
        include 'view/view_header.php';
        ?>
        <div class="row justify-content-center mx-auto sticky-top">
            <div class="col-11">
                <div class="card shadow bg-dark">
                    <div class="card-body px-4">
                        <div class="row align-items-center mb-4">
                            <div class="col-8">
                                <input type="text" class="form-control form-control-lg bg-dark border-secondary text-white border-1 fs-4" placeholder="Category Name">
                            </div>
                            <div class="col-4 d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-outline-success border border-success border-1 fs-4 flex-fill">Up</button>
                                <button type="button" class="btn btn-outline-success border border-success border-1 fs-4 flex-fill">Down</button>
                                <button type="button" class="btn btn-outline-success border border-success border-1 fs-4 flex-fill">Save</button>
                                <button type="button" class="btn btn-outline-danger border border-danger border-1 fs-4 flex-fill">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- FOOTER -->
    <footer class="fixed-bottom">
        <?php $is_guest = false ?> <!-- debug -->
        <?php include 'view/view_timer.php'; ?>
        <?php include 'view/view_footer.php'; ?>

    </footer>
</body>

</html>