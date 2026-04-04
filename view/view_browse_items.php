<?php require_once "utils/GoBack.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?= $web_root ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Items</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" type="text/css" href="css/item.css">
    <link rel="stylesheet" type="text/css" href="css/browse_items.css">
    <script src="lib/jquery-4.0.0.min.js" type="text/javascript"></script>
    <script src="scripts/search_fulltext_browse.js"></script>
</head>

<body class="d-flex flex-column min-vh-100 dark-bg" style="padding-bottom: 140px;">
    <?php
    $page_title = "Browse";
    include 'view/view_header.php';
    ?>

    <main class="flex-grow-1 content-box">
        <?php include 'view/view_search_bar.php'; ?>

        <div class="fw-bold fs-2 mt-4 mb-3" style="color: #2D68C4">Items I'm participating in</div>

        <?php if (!$is_guest): ?>
            <div class="d-flex flex-column" style="padding-bottom: 40px;">
                <div class="px-3 d-flex flex-column justify-content-evenly h-100">
                    <div id="participating_grid" class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
                        <?php if (!empty($user_bid_items)): ?>
                            <?php foreach ($user_bid_items as $element): ?>
                                <?php include 'view/view_my_item_card.php'; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-secondary mt-3 fs-3 col-12">No item found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="d-flex justify-content-center align-items-center p-5">
                <h1 class="text-secondary display-4 m-0">No Item Participation</h1>
            </div>
        <?php endif; ?>

        <div class="fw-bold fs-2 mb-3" style="color: #2D68C4;">
            <?= $is_guest ? "Available Items" : "Other Available Items" ?>
        </div>

        <div class="d-flex flex-column">
            <div class="px-3 d-flex flex-column justify-content-evenly h-100">
                <div id="available_grid" class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
                    <?php if (!empty($user_available_items)): ?>
                        <?php foreach ($user_available_items as $element): ?>
                            <?php include 'view/view_my_item_card.php'; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-secondary mt-3 fs-3 col-12">No item found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>

    <footer class="fixed-bottom">
        <?php include 'view/view_timer.php'; ?>
        <?php include 'view/view_footer.php'; ?>
    </footer>
</body>

</html>