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
    </main>

    <!-- FOOTER -->
    <footer class="fixed-bottom">
        <?php $is_guest = false ?> <!-- debug -->
        <?php include 'view/view_timer.php'; ?>
        <?php include 'view/view_footer.php'; ?>

    </footer>
</body>

</html>