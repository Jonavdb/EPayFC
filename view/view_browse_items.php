<?php require_once "utils/GoBack.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>">
    <title>Browse Items</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" type="text/css" href="css/item.css">
    <link rel="stylesheet" type="text/css" href="css/browse_items.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="d-flex flex-column min-vh-100 dark-bg" style="padding-bottom: 140px;">
    <?php
    $page_title = "Browse";
    include 'view/view_header.php';
    ?>

    <main class="flex-grow-1 content-box">
        <div class="fw-bold fs-2" style="color: #2D68C4">Items I'm participating in</div>
        <?php if (!empty($user_bid_items)): ?>
            <?php if (!$is_guest): ?>
                <div class="d-flex flex-column" style="padding-bottom: 40px;">
                    <div class="p-3 d-flex flex-column justify-content-evenly h-100">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">

                            <?php foreach ($user_bid_items as $item): ?>
                                <?php
                                // Image & Extra images logic
                                $img_src = (!empty($item->get_main_image_url()) && file_exists($item->get_main_image_url())) ? $item->get_main_image_url() : 'css/images/default_item_image.png';
                                $total_image_count = count($item->get_image_urls());
                                $extra_image_count = $total_image_count - 1;

                                // Bidder status logic
                                $highest_bid = $item->get_current_bid();
                                $user_bid_on_this_item = $item->get_user_bid_amount();
                                $is_highest_bidder = ($user_bid_on_this_item == $highest_bid);

                                // Time logic
                                $end = $item->get_end_at();
                                $now = new DateTime(AppTime::get_current_datetime());
                                $interval = $now->diff($end);
                                $is_future_deadline = !$interval->invert;
                                $isUrgent = ($interval->days < 1 && $is_future_deadline);
                                $time_color_class = $isUrgent ? 'text-warning' : 'text-success';
                                ?>

                                <div class="col">
                                    <a href="item/open/<?= $item->get_id() ?>/from-browse" class="card bg-dark h-100 text-decoration-none shadow-sm card-hover border-secondary">
                                        <div class="position-relative">
                                            <img src="<?= htmlspecialchars($img_src) ?>" class="card-img-top" alt="<?= htmlspecialchars($item->get_title()) ?>" style="height: 200px; object-fit: cover;">

                                            <div class="card-img-overlay d-flex flex-column justify-content-between p-2">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <?php if ($is_highest_bidder): ?>
                                                            <span class="badge rounded-pill bg-success"><i class="fa-solid fa-trophy"></i> Highest Bidder</span>
                                                        <?php else: ?>
                                                            <span class="badge rounded-pill bg-dark border border-secondary"><i class="fa-solid fa-gavel fa-flip-horizontal"></i> Bidder</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="d-flex flex-column align-items-end gap-1">
                                                        <span class="badge rounded-pill bg-warning text-dark"><i class="fa-solid fa-gavel fa-flip-horizontal"></i> Auction</span>
                                                        <?php if ($item->get_has_buy_now() && !$item->get_buy_now_reached()): ?>
                                                            <span class="badge rounded-pill bg-primary"><i class="fa-solid fa-bag-shopping"></i> Buy now</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <?php if ($extra_image_count > 0): ?>
                                                    <div class="d-flex justify-content-end">
                                                        <span class="badge rounded-pill text-bg-dark bg-opacity-75"><i class="fa-solid fa-images"></i> <?= $total_image_count ?> image<?= $total_image_count > 1 ? 's' : '' ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="card-body d-flex flex-column text-white">
                                            <div class="mb-2">
                                                <div class="text-start card-title text-truncate fw-bold fs-5 mb-0"><?= htmlspecialchars($item->get_title()) ?></div>
                                                <small class="text-secondary">by <?= htmlspecialchars($item->get_owner_name()) ?></small>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-end mt-auto mb-2 pt-2">
                                                <div class="d-flex flex-column">
                                                    <?php if ($item->get_has_buy_now()): ?>
                                                        <div class="fs-4 fw-bold text-primary">
                                                            € <?= number_format($item->get_buy_now_price(), 2, ',', '.') ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="fs-4 fw-bold text-warning">
                                                            € <?= number_format($item->get_starting_bid(), 2, ',', '.') ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="small fw-bold <?= $time_color_class ?> mt-1">
                                                        <i class="fa-regular fa-clock"></i> <?= $item->get_time_left() ?> left
                                                    </div>
                                                </div>

                                                <div class="text-end">
                                                    <div class="text-secondary small fw-bold mb-0">Current bid</div>
                                                    <div class="fs-4 fw-bold text-success">
                                                        € <?= number_format($item->get_current_bid(), 2, ',', '.') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="d-flex justify-content-center align-items-center p-5">
                <h1 class="text-secondary display-4 m-0">No Item Participation</h1>
            </div>
        <?php endif; ?>

        <div class="fw-bold fs-2" style="color: #2D68C4;">
            <?= $is_guest ? "Available Items" : "Other Available Items" ?>
        </div>

        <?php if (!empty($user_available_items)): ?>
            <div class="d-flex flex-column">
                <div class="p-3 d-flex flex-column justify-content-evenly h-100">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">

                        <?php foreach ($user_available_items as $item): ?>
                            <?php
                            // Image & Extra images logic
                            $img_src = (!empty($item->get_main_image_url()) && file_exists($item->get_main_image_url())) ? $item->get_main_image_url() : 'css/images/default_item_image.png';
                            $total_image_count = count($item->get_image_urls());
                            $extra_image_count = $total_image_count - 1;

                            // Time logic
                            $end = $item->get_end_at();
                            $now = new DateTime(AppTime::get_current_datetime());
                            $interval = $now->diff($end);
                            $is_future_deadline = !$interval->invert;
                            $isUrgent = ($interval->days < 1 && $is_future_deadline);
                            $time_color_class = $isUrgent ? 'text-warning' : 'text-success';
                            ?>

                            <div class="col">
                                <a href="item/open/<?= $item->get_id() ?>/from-browse" class="card bg-dark h-100 text-decoration-none shadow-sm card-hover border-secondary">
                                    <div class="position-relative">
                                        <img src="<?= htmlspecialchars($img_src) ?>" class="card-img-top" alt="<?= htmlspecialchars($item->get_title()) ?>" style="height: 200px; object-fit: cover;">

                                        <div class="card-img-overlay d-flex flex-column justify-content-between p-2">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div></div>
                                                <div class="d-flex flex-column align-items-end gap-1">
                                                    <?php if ($item->get_is_auction()): ?>
                                                        <span class="badge rounded-pill bg-warning text-dark"><i class="fa-solid fa-gavel fa-flip-horizontal"></i> Auction</span>
                                                    <?php endif; ?>
                                                    <?php if ($item->get_has_buy_now() && !$item->get_buy_now_reached()): ?>
                                                        <span class="badge rounded-pill bg-primary"><i class="fa-solid fa-bag-shopping"></i> Buy now</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <?php if ($extra_image_count > 0): ?>
                                                <div class="d-flex justify-content-end">
                                                    <span class="badge rounded-pill text-bg-dark bg-opacity-75"><i class="fa-solid fa-images"></i> <?= $total_image_count ?> image<?= $total_image_count > 1 ? 's' : '' ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="card-body d-flex flex-column text-white">
                                        <div class="mb-2">
                                            <div class="text-start card-title text-truncate fw-bold fs-5 mb-0"><?= htmlspecialchars($item->get_title()) ?></div>
                                            <small class="text-secondary">by <?= htmlspecialchars($item->get_owner_name()) ?></small>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-end mt-auto mb-2 pt-2">
                                            <div class="d-flex flex-column">
                                                <?php if ($item->get_has_buy_now()): ?>
                                                    <div class="fs-4 fw-bold text-primary">
                                                        € <?= number_format($item->get_buy_now_price(), 2, ',', '.') ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="fs-4 fw-bold text-warning">
                                                        € <?= number_format($item->get_starting_bid(), 2, ',', '.') ?>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="small fw-bold <?= $time_color_class ?> mt-1">
                                                    <i class="fa-regular fa-clock"></i> <?= $item->get_time_left() ?> left
                                                </div>
                                            </div>

                                            <?php if ($item->get_is_auction()): ?>
                                                <div class="text-end">
                                                    <div class="text-secondary small fw-bold mb-0">Current bid</div>
                                                    <div class="fs-4 fw-bold text-success">
                                                        € <?= number_format($item->get_current_bid(), 2, ',', '.') ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="d-flex justify-content-center align-items-center p-5">
                <h1 class="text-secondary display-4 m-0">No Available Items</h1>
            </div>
        <?php endif; ?>
    </main>

    <footer class="fixed-bottom">
        <?php include 'view/view_timer.php'; ?>
        <?php include 'view/view_footer.php'; ?>
    </footer>
</body>

</html>