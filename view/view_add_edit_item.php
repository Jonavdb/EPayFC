<?php

require_once "utils/GoBack.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>" />

    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
    <meta name="author" content="Jonathan_Vanderbiest">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Add item</title>
    <style>
        body {
            display: block;
            padding-top: 10px;
            padding-bottom: 80px;
            height: auto;
            justify-content: unset;
            align-items: unset;
        }

        #nav-icons {
            gap: 0;
            width: 100%;
            justify-content: space-around;
            flex-direction: row;
        }
    </style>
</head>

<body>
    <nav class="navbar fixed-top navbar-dark bg-dark">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <a href="<?= GoBack::go_back() ?>" class="btn-icon">
                    <i class="fa-solid fa-arrow-left" style="color: #2D68C4; font-size: 22px;"></i>
                </a>
            </div>
            <div class="flex-grow-1 text-center">
                <h2 style="color: #2D68C4; margin: 0;">
                    <?php $item->id > 0 ? 'Edit Item' : 'Add item' ?>
                </h2>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit"
                    form="itemForm"
                    class="btn fw-bold fs-4 text-white">
                    Save
                </button>
            </div>
        </div>
    </nav>
    <?php
    $is_edit = ($item->id > 0);
    $action_url = $is_edit ? "index.php?controller=Item&action=edit" : "index.php?controller=Item&action=add";
    ?>

    <form id="itemForm" action="<?= $action_url ?>" method="POST" class="bg-transparent">

        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $item->id ?>">
            <input type="hidden" name="param1" value="<?= $item->id ?>">
        <?php endif; ?>
        <div class="container-fluid mb-4 px-5" style="margin-top: 30px;">
            <div class="card text-bg-dark border-secondary">
                <div class="card-header border-secondary" style="background-color: #1e293b;">
                    <h5 class="mb-0 text-white text-start">Basic Informations</h5>
                </div>
                <div class="card-body">
                    <div class=" mb-3">
                        <label for="title" class="form-label fw-bold">Item Title *</label>
                        <input type="text"
                            class="form-control bg-black bg-opacity-25 text-white border-secondary <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                            id="title"
                            name="title"
                            value="<?php echo html_entity_decode($item->title) ?>">
                        <?php if (isset($errors['title'])) { ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['title'] ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control bg-black bg-opacity-25 text-white border-secondary"
                            id="description"
                            name="description"
                            rows="4"><?php echo html_entity_decode($item->description) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label fw-bold">Sale Duration(days) * </label>
                        <input type="number"
                            class="form-control bg-black bg-opacity-25 text-white border-secondary <?= isset($errors['duration']) ? 'is-invalid' : '' ?>"
                            id="duration"
                            name="duration"
                            value="<?php echo $item->duration_days ?>">
                        <?php if (isset($errors['duration'])) { ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['duration'] ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid mb-4 px-5">
            <div class="card text-bg-dark border-secondary">
                <div class="card-header border-secondary" style="background-color: #1e293b;">
                    <h5 class="mb-0 text-white text-start">Sale Type</h5>
                </div>

                <div class="card-body">

                    <div class="card bg-dark border-primary mb-3">
                        <div class="card-header border-primary text-primary bg-primary bg-opacity-10 fw-bold">
                            <i class="fa-solid fa-gavel me-2"></i> Option 1: Auction
                        </div>

                        <div class="card-body">
                            <div class="mb-3">
                                <label for="starting_bid" class="form-label fw-bold text-white">Starting Bid</label>
                                <div class="input-group">
                                    <input type="number"
                                        class="form-control bg-black bg-opacity-25 text-white border-secondary <?php echo (isset($errors['starting_bid']) || isset($errors['sale'])) ? 'is-invalid' : '' ?>"
                                        id="starting_bid"
                                        name="starting_bid"
                                        value="<?php echo ($item->starting_bid > 0) ? $item->starting_bid : '' ?>">
                                    <span class="input-group-text bg-dark text-white border-secondary">€</span>

                                    <?php if (isset($errors['starting_bid'])) { ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['starting_bid'] ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (isset($errors['sale'])) { ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['sale'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="instant" class="form-label fw-bold text-white">Instant Purchase Price (optional)</label>
                                <div class="input-group">
                                    <input type="number"
                                        class="form-control bg-black bg-opacity-25 text-white border-secondary <?php echo isset($errors['buy_now_price']) ? 'is-invalid' : '' ?>"
                                        id="instant"
                                        name="instant"
                                        value="<?php echo ($item->has_buy_now && $item->is_auction && $item->buy_now_price > 0) ? $item->buy_now_price : '' ?>">
                                    <span class="input-group-text bg-dark text-white border-secondary">€</span>
                                    <?php if (isset($errors['buy_now_price'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['buy_now_price'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-dark border-success mb-0">
                        <div class="card-header border-success text-success bg-success bg-opacity-10 fw-bold">
                            <i class="fa-solid fa-cart-shopping me-2"></i> Option 2: Direct Sale
                        </div>

                        <div class="card-body">
                            <div class="mb-0">
                                <label for="direct" class="form-label fw-bold text-white">Sale Price</label>
                                <div class="input-group">
                                    <input type="number"
                                        class="form-control bg-black bg-opacity-25 text-white border-secondary <?php echo isset($errors['sale_price']) ? 'is-invalid' : '' ?>"
                                        id="direct"
                                        name="direct"
                                        value="<?php echo ($item->is_direct_sale && $item->buy_now_price > 0) ? $item->buy_now_price : ''  ?>">
                                    <span class="input-group-text bg-dark text-white border-secondary">€</span>
                                    <?php if (isset($errors['sale_price'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['sale_price'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
    <div class="fixed-bottom">

        <nav class="navbar navbar-dark bg-dark">
            <ul class="navbar-nav bottom-nav" id="nav-icons">
                <li class="nav-item text-center">
                    <a href="item/browse" class="nav-link"><i class="fa-solid fa-magnifying-glass d-block"></i><small style="font-size: 10px;">Browse</small></a>
                </li>
                <li class="nav-item text-center">
                    <a href="item/list" class="nav-link"><i class="fa-solid fa-house d-block"></i><small style="font-size: 10px;">My Items</small></a>
                </li>
                <li class="nav-item text-center">
                    <a href="item/add" class="nav-link"><i class="fa-solid fa-plus d-block"></i><small style="font-size: 10px;">Add Offer</small></a>
                </li>
                <li class="nav-item text-center">
                    <a href="user/profile" class="nav-link"><i class="fa-solid fa-gear d-block"></i><small style="font-size: 10px;">Profile</small></a>
                </li>
            </ul>
        </nav>

        <div class="d-flex justify-content-between align-items-center px-3" style="
        background-color: #1e293b; 
        border-top: 1px solid #334155; 
        color: #94a3b8;
        height: 40px;
        font-size: 11px;
    ">
            <span class="fw-bold text-white">
                <?php
                $dt = new DateTime(AppTime::get_current_datetime());
                echo $dt->format('d/m/y H:i');
                ?>
            </span>

            <div class="d-flex align-items-center">

                <form method="post" action="time/advance" class="m-0">
                    <input type="hidden" name="amount" value="1"><input type="hidden" name="unit" value="hour">
                    <button class="btn btn-debug text-debug-orange">
                        <i class="fa-regular fa-clock me-1"></i>+1h
                    </button>
                </form>

                <form method="post" action="time/advance" class="m-0">
                    <input type="hidden" name="amount" value="1"><input type="hidden" name="unit" value="day">
                    <button class="btn btn-debug text-debug-orange">
                        <i class="fa-regular fa-calendar me-1"></i>+1d
                    </button>
                </form>

                <form method="post" action="time/advance" class="m-0">
                    <input type="hidden" name="amount" value="7"><input type="hidden" name="unit" value="day">
                    <button class="btn btn-debug text-debug-orange">
                        <i class="fa-regular fa-calendar-days me-1"></i>+1w
                    </button>
                </form>

                <form method="post" action="time/reset" class="m-0">
                    <button class="btn btn-debug text-debug-red">
                        <i class="fa-solid fa-rotate-right me-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    </div>
</body>

</html>