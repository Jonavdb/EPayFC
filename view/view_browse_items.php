<?php require_once "utils/GoBack.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>">
    <title>Browse Items</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" type="text/css" href="css/item.css">
    <link rel="stylesheet" type="text/css" href="css/browse_items.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="d-flex flex-column min-vh-100 dark-bg" style="padding-bottom: 140px;">
    <!-- HEADER -->
    <?php
    $page_title = "Browse";
    include 'view/view_header.php';
    ?>

    <!-- PAGE CONTENT -->
    <main class="flex-grow-1 content-box">

        <!-- CONTAINER "Items I'm participating in"-->
        <?php if (!$is_guest): ?>
            <div class="d-flex flex-column min-vh-100">
                <!-- Text : "Items i'm participating in" -->
                <div class="fw-bold fs-2" style="color: #2D68C4;">Items I'm participating in</div>

                <!-- Vertical container that takes remaining space automatically -->
                <div class="p-3 d-flex flex-column justify-content-evenly h-100">

                    <!-- Item cards wrapping container -->
                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 ">
                        <!-- 1 item / col -->
                        <?php foreach ($user_bid_items as $item) { ?>
                            <div class="col">
                                <a href="item/open/<?= $item->id ?>/from-browse" class="card dark-theme w-100 h-100 text-decoration-none">
                                    <div class="d-flex flex-column h-100">

                                        <!-- TOP CELL (50%) -->
                                        <div class="row flex-fill" style="height:50%;">
                                            <div class="col-sm h-100 position-relative">
                                                <img class="card-img-top h-100"
                                                    src="<?= $item->main_image_url ?>"
                                                    alt="Auction Item Image">

                                                <!-- Everything that is on top of the item image -->
                                                <div class="card-img-overlay d-flex flex-column justify-content-between" style="height:100%; padding:1rem;">

                                                    <!-- Row 1 : Top left & Top right -->
                                                    <div class="d-flex justify-content-between">

                                                        <!-- Top left -->
                                                        <div>
                                                            <?php
                                                            $highest_bid = $item->current_bid;
                                                            $user_bid_on_this_item = $item->user_bid;
                                                            $is_highest_bidder = $user_bid_on_this_item == $highest_bid;
                                                            ?>
                                                            <?php if ($is_highest_bidder) { ?>
                                                                <span class="badge rounded-pill bg-success badge-bidder d-inline-flex align-items-center justify-content-center px-3 py-2">
                                                                    <i class="fa-solid fa-trophy me-2"></i> Highest Bidder
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="badge rounded-pill bg-dark badge-bidder d-inline-flex align-items-center justify-content-center px-3 py-2">
                                                                    <i class="fa-solid fa-gavel flipped-horizontal me-2"></i> Bidder
                                                                </span>
                                                            <?php } ?>
                                                        </div>

                                                        <!-- Top right -->
                                                        <div class="d-flex flex-column gap-2">
                                                            <span class="badge rounded-pill bg-warning text-dark badge-auction d-inline-flex align-items-center justify-content-center px-3 py-2 mb-1">
                                                                <i class="fa-solid fa-gavel flipped-horizontal me-2"></i> Auction
                                                            </span>

                                                            <?php
                                                            $has_buy_now = $item->has_buy_now;
                                                            $buy_now_reached = $item->buy_now_reached;
                                                            ?>
                                                            <?php if ($has_buy_now && !$buy_now_reached) { ?>
                                                                <span class="badge rounded-pill bg-primary d-inline-flex align-items-center justify-content-center px-3 py-2">
                                                                    <i class="fa-solid fa-bag-shopping me-2"></i> Buy now
                                                                </span>
                                                            <?php } ?>
                                                        </div>
                                                    </div>

                                                    <!-- Row 2 : Bottom right -->
                                                    <?php
                                                    // Get the total number of images
                                                    $total_image_count = count($item->image_urls);
                                                    // Calculate the number of extra images (excluding the main one)
                                                    $extra_image_count = $total_image_count - 1;
                                                    ?>
                                                    <?php if ($extra_image_count > 0) { ?>
                                                        <div class="d-flex justify-content-end">
                                                            <span class="badge rounded-pill bg-dark d-inline-flex align-items-center justify-content-center px-3 py-2">
                                                                <i class="fa-solid fa-images me-2"></i>
                                                                <?= $extra_image_count ?>
                                                                <?= ($extra_image_count === 1) ? ' image' : ' images' ?>
                                                            </span>
                                                        </div>
                                                    <?php } ?>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- BOTTOM CELL (50%) -->
                                        <div class="row flex-fill" style="height:50%;">
                                            <div class="col-sm h-100">
                                                <div class="card-body h-100 bg-dark text-white">

                                                    <!-- Direction : Vertical -->
                                                    <div class="d-flex flex-column h-100">

                                                        <!-- Row 1 : Top left (using bootstrap class "card title") -->
                                                        <div class="card-title">
                                                            <b><?= $item->title ?></b>
                                                            <div class="fs-5 fw-bold text-secondary"><?= $item->owner_name ?></div>
                                                        </div>

                                                        <!-- Row 2 : Middle left & Middle right (using bootstrap class "card text") -->
                                                        <div class="d-flex flex-column justify-content-around flex-grow-1">

                                                            <div class="card-text d-flex justify-content-between price-row mt-2 mb-2">

                                                                <!-- Mid left -->
                                                                <?php $has_buy_now = $item->has_buy_now;  ?>

                                                                <div class="price-left d-flex align-items-center fs-3 text-primary fw-bold">
                                                                    <?php if ($has_buy_now) { ?>
                                                                        € <?= number_format($item->buy_now_price, 2, ',', '.') ?>
                                                                    <?php } ?>
                                                                </div>

                                                                <!-- Mid right -->
                                                                <div class="d-flex flex-column text-end bid-info">
                                                                    <div class="fs-5 fw-bold text-secondary">Current bid</div>
                                                                    <div class="bid-price fs-4 text-success fw-bold">
                                                                        € <?= number_format($item->current_bid, 2, ',', '.') ?>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <!-- Row 3 : Bottom left (below boostrap class "card text")-->
                                                            <div class="time-left fs-5 text-secondary fw-bold">
                                                                <i class="fa-solid fa-clock transparent-icon fs-5 fw-bold text-secondary"></i>
                                                                <?= $item->time_left ?> left
                                                            </div>


                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </a>
                            </div>

                        <?php } ?>
                    </div>
                </div>

            </div>
        <?php endif; ?>

        <!-- CONTAINER 2 "Other available items" -->
        <div class="d-flex flex-column min-vh-100">

            <!-- Text : "Items i'm participating in" -->
            <?php if ($is_guest): ?>
                <div class="fw-bold fs-2" style="color: #2D68C4;">Available Items</div>
            <?php else: ?>
                <div class="fw-bold fs-2" style="color: #2D68C4;">Other Available Items</div>
            <?php endif; ?>


            <!-- Vertical container that takes remaining space automatically -->
            <div class="p-3 d-flex flex-column justify-content-evenly h-100">

                <!-- Item cards wrapping container -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
                    <!-- 1 item / col -->
                    <?php foreach ($user_available_items as $item) { ?>
                        <div class="col">
                            <a href="item/open/<?= $item->id ?>/from-browse" class="card bg-dark w-100 h-100 text-decoration-none">
                                <div class="d-flex flex-column h-100">

                                    <!-- TOP CELL (50%) -->
                                    <div class="row flex-fill" style="height:50%;">
                                        <div class="col-sm h-100 position-relative">
                                            <img class="card-img-top h-100" src="<?= $item->main_image_url ?>" alt="Auction Item Image">

                                            <div class="card-img-overlay d-flex flex-column justify-content-between" style="height:100%; padding:1.2rem;">

                                                <!-- Top row: Top-left empty, top-right badges -->
                                                <div class="d-flex justify-content-between">
                                                    <div></div>
                                                    <div class="d-flex flex-column gap-2">
                                                        <?php if ($item->is_auction): ?>
                                                            <span class="badge rounded-pill bg-warning d-inline-flex align-items-center justify-content-center px-3 py-2">
                                                                <i class="fa-solid fa-gavel flipped-horizontal me-2"></i> Auction
                                                            </span>
                                                        <?php endif; ?>

                                                        <?php if ($item->has_buy_now && !$item->buy_now_reached): ?>
                                                            <span class="badge rounded-pill bg-primary d-inline-flex align-items-center justify-content-center px-3 py-2">
                                                                <i class="fa-solid fa-bag-shopping me-2"></i> Buy now
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- Bottom-right extra images badge -->
                                                <?php
                                                $total_image_count = count($item->image_urls);
                                                $extra_image_count = $total_image_count - 1;
                                                ?>
                                                <?php if ($extra_image_count > 0): ?>
                                                    <div class="d-flex justify-content-end">
                                                        <span class="badge rounded-pill bg-dark d-inline-flex align-items-center justify-content-center px-3 py-2">
                                                            <i class="fa-solid fa-images me-2"></i>
                                                            <?= $extra_image_count ?>
                                                            <?= ($extra_image_count === 1) ? ' image' : ' images' ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- BOTTOM CELL (50%) -->
                                    <div class="row flex-fill" style="height:50%;">
                                        <div class="col-sm h-100">
                                            <div class="card-body h-100 bg-dark text-white">
                                                <div class="d-flex flex-column h-100">

                                                    <!-- Row 1: Title & Owner -->
                                                    <div class="card-title">
                                                        <b><?= $item->title ?></b>
                                                        <div class="fs-5 fw-bold text-secondary"><?= $item->owner_name ?></div>
                                                    </div>

                                                    <!-- Row 2: Prices -->
                                                    <div class="d-flex flex-column justify-content-around flex-grow-1">

                                                        <div class="card-text d-flex justify-content-between price-row mt-2 mb-2">

                                                            <!-- Left: Buy now price -->
                                                            <div class="price-left d-flex align-items-center fs-3 text-primary fw-bold">
                                                                <?php if ($item->has_buy_now): ?>
                                                                    € <?= number_format($item->buy_now_price, 2, ',', '.') ?>
                                                                <?php endif; ?>
                                                            </div>

                                                            <!-- Right: Current bid -->
                                                            <?php if ($item->is_auction): ?>
                                                                <div class="d-flex flex-column text-end bid-info">
                                                                    <div class="fs-5 fw-bold text-secondary">Current bid</div>
                                                                    <div class="bid-price fs-4 text-success fw-bold">
                                                                        € <?= number_format($item->current_bid, 2, ',', '.') ?>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                        </div>

                                                        <!-- Row 3: Time left -->
                                                        <div class="time-left fs-5 text-secondary fw-bold">
                                                            <i class="fa-solid fa-clock transparent-icon fs-5 fw-bold text-secondary"></i>
                                                            <?= $item->time_left ?> left
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </a>
                        </div>

                    <?php } ?>
                </div>
            </div>

        </div>
    </main>

    <footer class="fixed-bottom">

        <?php include 'view/view_footer.php'; ?>
        <?php include 'view/view_timer.php'; ?>


    </footer>
</body>

</html>