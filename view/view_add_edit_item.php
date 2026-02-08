<?php
require_once "utils/GoBack.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Jonathan_Vanderbiest">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <title>Add item</title>
</head>

<body style="padding-top:60px; padding-bottom: 140px;">
    <?php
    $page_title = ($item->id > 0) ? 'Edit Item' : 'Add Item';

    // On cache l'icône "panier" pour cette page
    $hide_default_icon = true;

    // On utilise ob_start() pour capturer le HTML proprement, ou juste une string
    ob_start();
    ?>
    <button type="submit" form="itemForm" class="btn-icon text-primary">
        <i class="fa-regular fa-floppy-disk fa-lg"></i>
    </button>
    <?php
    $header_right_content = ob_get_clean();

    include 'view/view_header.php';
    ?>

    <?php
    $is_edit = ($item->id > 0);
    $action_url = $is_edit ? "item/edit/$item->id" : "item/add";
    ?>

    <form id="itemForm" action="<?= $action_url ?>" method="POST" class="bg-transparent border-0 shadow-none">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $item->id ?>">
            <input type="hidden" name="param1" value="<?= $item->id ?>">
        <?php endif; ?>
        <div class="container mb-4" style="margin-top: 30px;">
            <div class="card text-bg-dark border-secondary">
                <div class="card-header border-secondary" style="background-color: #1e293b;">
                    <h5 class="mb-0 text-white text-start">Basic Informations</h5>
                </div>
                <div class="card-body">
                    <div class=" mb-3">
                        <label for="title" class="form-label fw-bold">Item Title *</label>
                        <input type="text"
                            class="form-control bg-black bg-opacity-25 text-white border-secondary <?= isset($errors['title']) || isset($errors['empty']) ? 'is-invalid' : '' ?>"
                            id="title"
                            name="title"
                            value="<?php echo html_entity_decode($item->title) ?>">
                        <?php if (isset($errors['empty'])) { ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['empty'] ?>
                            </div>
                        <?php } ?>

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

        <div class="container mb-4">
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
                                        class="form-control bg-black bg-opacity-25 text-white border-secondary <?php echo (isset($errors['starting_bid']) || isset($errors['sale']) || isset($errors['sale_price'])) ? 'is-invalid' : '' ?>"
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
                                    <?php if (isset($errors['sale_price'])) { ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['sale_price'] ?>
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
                                        value="<?php echo isset($_POST['direct']) ? $_POST['direct'] : (($item->is_direct_sale && $item->buy_now_price > 0) ? $item->buy_now_price : '') ?>">
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

    <footer class="fixed-bottom">
        <?php include 'view/view_timer.php'; ?>
        <?php include 'view/view_footer.php'; ?>
    </footer>
</body>

</html>