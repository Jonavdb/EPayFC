<?php require_once "utils/GoBack.php"; ?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>My Items</title>
    <base href="<?= $web_root ?>">
    <meta name="author" content="HABOULA_Ali">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <script src="lib/jquery-4.0.0.min.js" type="text/javascript"></script>
    <script src="scripts/search_fulltext_my_items.js"></script>
</head>

<body>
    <main class="container" style="padding-top: 80px; padding-bottom: 100px;">
        <!-- HEADER -->
        <?php
        $page_title = "My Items";
        include 'view/view_header.php';
        ?>

        <!-- BARRE DE RECHERCHE -->
        <?php include 'view/view_search_bar.php'; ?>

        <!-- ACTIVE ITEMS -->
        <h2 class="text-start fw-bold text-primary mb-3 mt-4">Active Items</h2>
        <div id="active_items_grid" class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-6">
            <?php if (!empty($active_items)): ?>
                <?php foreach ($active_items as $element): ?>
                    <?php include 'view_my_item_card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-secondary mt-3 fs-3">No item found.</p>
            <?php endif; ?>
        </div>

        <!-- CLOSED UNSOLD ITEMS -->
        <h2 class="text-start fw-bold text-primary mb-3 mt-5">Closed Unsold Items</h2>
        <div id="closed_unsold_items_grid" class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-6">
            <?php if (!empty($unsold_items)): ?>
                <?php foreach ($unsold_items as $element): ?>
                    <?php include 'view_my_item_card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-secondary mt-3 fs-3">No item found.</p>
            <?php endif; ?>
        </div>

        <!-- SOLD ITEMS -->
        <h2 class="text-start fw-bold text-primary mb-3 mt-5">Sold Items</h2>
        <div id="sold_items_grid" class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-6">
            <?php if (!empty($sold_items)): ?>
                <?php foreach ($sold_items as $element): ?>
                    <?php include 'view_my_item_card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-secondary mt-3 fs-3">No item found.</p>
            <?php endif; ?>
        </div>

        <?php if (empty($active_items) && empty($unsold_items) && empty($sold_items)): ?>
            <div class=" text-center mt-5 pt-5 text-secondary">
                <i class="fa-solid fa-box-open fa-4x mb-3"></i>
                <p class="fs-4">You haven't posted any items yet.</p>
                <a href="item/add" class="btn btn-primary btn-lg mt-2">Start selling</a>
            </div>
        <?php endif; ?>

    </main>

    <!-- FOOTER -->
    <footer class="fixed-bottom">
        <?php include 'view/view_footer.php'; ?>
    </footer>

</body>

</html>