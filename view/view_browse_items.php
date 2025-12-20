view

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Browse Items</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" type="text/css" href="css/item.css">
    <link rel="stylesheet" type="text/css" href="css/browse_items.css">

    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- HEADER -->
    <header class="content-box">
        <nav class="navbar fixed-top navbar-dark bg-dark border-top shadow-lg">
            <div class="container-fluid d-flex align-items-center">
                <div class="flex-grow-1 text-center">
                    <div class="fw-bold fs-2" style="color: #2D68C4;"> Browse <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- PAGE CONTENT -->
    <main class="flex-grow-1 content-box">

        <!-- CONTAINER "Items I'm participating in"-->
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
                            <a href='#' class="card dark-theme w-100 h-100 text-decoration-none">
                                <!-- TOP CELL -->
                                <div class="row">
                                    <div class="col-sm">
                                        <img class="card-img-top" src=<?= $item->main_image_url ?> alt="Auction Item Image">

                                        <!-- Everything that is on top of the item image -->
                                        <div class="card-img-overlay" style="height:57%;">

                                            <!-- Direction : Vertical -->
                                            <div class="d-flex flex-column h-100">

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
                                                            <span class="badge rounded-pill bg-success badge-bidder">
                                                                <i class="fa-solid fa-trophy"></i> Highest Bidder
                                                            </span>
                                                        <?php } else { ?>
                                                            <span class="badge rounded-pill bg-dark badge-bidder">
                                                                <i class="fa-solid fa-gavel flipped-horizontal"></i> Bidder
                                                            </span>
                                                        <?php } ?>
                                                    </div>

                                                    <!-- Top right -->
                                                    <div class="d-flex flex-column">
                                                        <span class="badge rounded-pill bg-warning text-dark badge-auction mb-1">
                                                            <i class="fa-solid fa-gavel flipped-horizontal"></i> Auction
                                                        </span>

                                                        <?php
                                                        $has_buy_now = $item->has_buy_now;
                                                        $buy_now_reached = $item->buy_now_reached;
                                                        ?>

                                                        <?php if ($has_buy_now && !$buy_now_reached) { ?>
                                                            <span class="badge rounded-pill bg-primary">
                                                                <i class="fa-solid fa-bag-shopping"></i> Buy now
                                                            </span>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <!-- Spacer between Row 1 and Row 2 -->
                                                <div class="flex-grow-1"></div>

                                                <!-- Row 2 : Bottom right -->
                                                <?php
                                                // Get the total number of images
                                                $total_image_count = count($item->image_urls);
                                                // Calculate the number of extra images (excluding the main one)
                                                $extra_image_count = $total_image_count - 1;
                                                ?>

                                                <?php if ($extra_image_count > 0) { ?>
                                                    <div class="d-flex justify-content-end">
                                                        <span class="badge rounded-pill bg-dark">
                                                            <i class="fa-solid fa-images"></i>
                                                            <?= $extra_image_count ?>
                                                            <?= ($extra_image_count === 1) ? ' image' : ' images' ?>
                                                        </span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BOTTOM CELL -->
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="card-body">

                                            <!-- Direction : Vertical -->
                                            <div class="d-flex flex-column">

                                                <!-- Row 1 : Top left (using bootstrap class "card title") -->
                                                <div class="card-title">
                                                    <b><?= $item->title ?></b>
                                                    <div><?= $item->owner_name  ?></div>
                                                </div>

                                                <!-- Row 2 : Middle left & Middle right (using bootstrap class "card text") -->
                                                <div class="card-text d-flex justify-content-between price-row mt-2 mb-2">

                                                    <!-- Mid left -->
                                                    <?php
                                                    $has_buy_now = $item->has_buy_now;
                                                    $buy_now_reached = $item->buy_now_reached;
                                                    ?>

                                                    <?php if ($has_buy_now) { ?>

                                                        <?php if ($buy_now_reached) { ?>
                                                            <div class="price-left text-warning">€ <?= number_format($item->buy_now_price, 2, ',', '.') ?></div>
                                                        <?php } else { ?>
                                                            <div class="price-left">€ <?= number_format($item->buy_now_price, 2, ',', '.') ?></div>
                                                        <?php } ?>

                                                    <?php } else { ?>
                                                        <div></div>
                                                    <?php } ?>

                                                    <!-- Mid right -->
                                                    <div class="d-flex flex-column text-end bid-info">
                                                        <div>Current bid</div>
                                                        <div class="bid-price">€ <?= number_format($item->buy_now_price, 2, ',', '') ?></div>
                                                    </div>
                                                </div>

                                                <!-- Row 3 : Bottom left (below boostrap class "card text"-->
                                                <div class="time-left">
                                                    <i class="fa-solid fa-clock transparent-icon"></i> <?= $item->time_left ?> left
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

        <!-- CONTAINER 2 "Other available items" -->
        <div class="d-flex flex-column min-vh-100">

            <!-- Text : "Items i'm participating in" -->
            <div class="fw-bold fs-2" style="color: #2D68C4;">Other Available Items</div>

            <!-- Vertical container that takes remaining space automatically -->
            <div class="p-3 d-flex flex-column justify-content-evenly h-100">

                <!-- Item cards wrapping container -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 ">
                    <!-- 1 item / col -->
                    <?php foreach ($user_available_items as $item) { ?>
                        <div class="col">
                            <a href='#' class="card dark-theme w-100 h-100 text-decoration-none">
                                <!-- TOP CELL -->
                                <div class="row">
                                    <div class="col-sm">
                                        <img class="card-img-top" src=<?= $item->main_image_url ?> alt="Auction Item Image">

                                        <!-- Everything that is on top of the item image -->
                                        <div class="card-img-overlay" style="height:57%;">

                                            <!-- Direction : Vertical -->
                                            <div class="d-flex flex-column h-100">

                                                <!-- Row 1 : Top left & Top right -->
                                                <div class="d-flex justify-content-between">

                                                    <!-- Top left -->
                                                    <div>
                                                    </div>

                                                    <!-- Top right -->
                                                    <div class="d-flex flex-column">
                                                        <?php if ($item->is_auction) { ?>
                                                            <span class="badge rounded-pill bg-warning text-dark badge-auction mb-1">
                                                                <i class="fa-solid fa-gavel flipped-horizontal"></i> Auction
                                                            </span>
                                                        <?php } ?>

                                                        <?php
                                                        $has_buy_now = $item->has_buy_now;
                                                        $buy_now_reached = $item->buy_now_reached;
                                                        ?>

                                                        <?php if ($has_buy_now && !$buy_now_reached) { ?>
                                                            <span class="badge rounded-pill bg-primary">
                                                                <i class="fa-solid fa-bag-shopping"></i> Buy now
                                                            </span>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <!-- Spacer between Row 1 and Row 2 -->
                                                <div class="flex-grow-1"></div>

                                                <!-- Row 2 : Bottom right -->
                                                <?php
                                                // Get the total number of images
                                                $total_image_count = count($item->image_urls);
                                                // Calculate the number of extra images (excluding the main one)
                                                $extra_image_count = $total_image_count - 1;
                                                ?>

                                                <?php if ($extra_image_count > 0) { ?>
                                                    <div class="d-flex justify-content-end">
                                                        <span class="badge rounded-pill bg-dark">
                                                            <i class="fa-solid fa-images"></i>
                                                            <?= $extra_image_count ?>
                                                            <?= ($extra_image_count === 1) ? ' image' : ' images' ?>
                                                        </span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BOTTOM CELL -->
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="card-body">

                                            <!-- Direction : Vertical -->
                                            <div class="d-flex flex-column">

                                                <!-- Row 1 : Top left (using bootstrap class "card title") -->
                                                <div class="card-title">
                                                    <b><?= $item->title ?></b>
                                                    <div><?= $item->owner_name  ?></div>
                                                </div>

                                                <!-- Row 2 : Middle left & Middle right (using bootstrap class "card text") -->
                                                <div class="card-text d-flex justify-content-between price-row mt-2 mb-2">

                                                    <!-- Mid left -->
                                                    <?php
                                                    $has_buy_now = $item->has_buy_now;
                                                    $buy_now_reached = $item->buy_now_reached;
                                                    ?>

                                                    <?php if ($has_buy_now) { ?>

                                                        <?php if ($buy_now_reached) { ?>
                                                            <div class="price-left text-warning">€ <?= number_format($item->buy_now_price, 2, ',', '.') ?></div>
                                                        <?php } else { ?>
                                                            <div class="price-left">€ <?= number_format($item->buy_now_price, 2, ',', '.') ?></div>
                                                        <?php } ?>

                                                    <?php } else { ?>
                                                        <div></div>
                                                    <?php } ?>

                                                    <!-- Mid right -->
                                                    <?php if ($item->is_auction) { ?>
                                                        <div class="d-flex flex-column text-end bid-info">
                                                            <div>Current bid</div>
                                                            <div class="bid-price">€ <?= number_format($item->current_bid, 2, ',', '') ?></div>
                                                        </div>
                                                    <?php } else { ?>

                                                        <div></div>
                                                    <?php } ?>
                                                </div>

                                                <!-- Row 3 : Bottom left (below boostrap class "card text"-->
                                                <div class="time-left">
                                                    <i class="fa-solid fa-clock transparent-icon"></i> <?= $item->time_left ?> left
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

    <!-- FOOTER -->
    <footer class="content-box">
        <nav class="navbar fixed-bottom navbar-dark bg-dark border-top shadow-lg">
            <div class="container-fluid">

                <div class="d-flex justify-content-around w-100 py-1">

                    <a class="nav-link text-center text-secondary" href="main/index">
                        <i class="fa-solid fa-magnifying-glass-plus fs-5 mb-1 d-block"></i>
                        <span>Browse</span>
                    </a>

                    <a class="nav-link text-center text-secondary" href="member/items">
                        <i class="fas fa-home fs-5 mb-1 d-block"></i>
                        <span>My Items</span>
                    </a>

                    <?php
                    // On récupère le user connecté (s'il existe)
                    $is_real_user = isset($_SESSION['user']) && !empty($_SESSION['user']);
                    ?>

                    <?php if ($is_real_user): ?>

                        <a class="nav-link text-center text-secondary" href="member/add_offer">
                            <i class="fa-solid fa-plus fs-5 mb-1 d-block"></i>
                            <span>Add Offer</span>
                        </a>

                        <a class="nav-link text-center text-secondary" href="user/profile">
                            <i class="fa-solid fa-gear fs-5 mb-1 d-block"></i>
                            <span>Profile</span>
                        </a>

                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </footer>
</body>

</html>