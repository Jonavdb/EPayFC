<?php
require_once "utils/GoBack.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add item</title>
    <base href="<?= $web_root ?>" />
    <meta name="author" content="Jonathan_Vanderbiest">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <script src="lib/jquery-4.0.0.min.js" type="text/javascript"></script>
    <script src="scripts/add_edit_item.js"></script>
</head>

<body style="padding-top:60px; padding-bottom: 140px;">
    <?php
    $page_title = ($item->get_id() > 0) ? 'Edit Item' : 'Add Item';

    // On cache l'icône "panier" pour cette page
    $hide_default_icon = true;

    // On utilise ob_start() pour capturer le HTML proprement, ou juste une string
    ob_start();
    ?>
    <button type="submit" id="submitBtn" form="itemForm" class="btn-icon text-primary" disabled="disabled">
        <i class="fa-regular fa-floppy-disk fa-lg"></i>
    </button>
    <?php
    $header_right_content = ob_get_clean();

    include 'view/view_header.php';
    ?>

    <?php
    $is_edit = ($item->get_id() > 0);
    $action_url = $is_edit ? "item/edit/" . $item->get_id() : "item/add";
    ?>

    <form id="itemForm" action="<?= $action_url ?>" method="POST" class="bg-transparent border-0 shadow-none">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $item->get_id() ?>">
            <input type="hidden" name="param1" value="<?= $item->get_id() ?>">
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
                            class="form-control bg-black bg-opacity-25 text-white border-secondary"
                            id="title"
                            name="title"
                            placeholder="Ex: IPhone 13 Pro Max 256GB"
                            value="<?php echo html_entity_decode($item->get_title()) ?>">
                        <div id="errTitle"></div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control bg-black bg-opacity-25 text-white border-secondary"
                            id="description"
                            name="description"
                            placeholder="Describe your item in detail..."
                            rows="4"><?php echo html_entity_decode($item->get_description()) ?></textarea>
                        <div id="errDescription"></div>
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label fw-bold">Sale Duration(days) * </label>
                        <input type="number"
                            class="form-control bg-black bg-opacity-25 text-white border-secondary"
                            id="duration"
                            name="duration"
                            value="<?php echo $item->get_duration_days() ?>">
                        <div id="errDuration"></div>
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
                                        value="<?php echo ($item->get_starting_bid() > 0) ? $item->get_starting_bid() : '' ?>">
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
                                        value="<?php echo ($item->get_has_buy_now() && $item->get_is_auction() && $item->get_buy_now_price() > 0) ? $item->get_buy_now_price() : '' ?>">
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
                                        value="<?php echo isset($_POST['direct']) ? $_POST['direct'] : (($item->get_is_direct_sale() && $item->get_buy_now_price() > 0) ? $item->get_buy_now_price() : '') ?>">
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