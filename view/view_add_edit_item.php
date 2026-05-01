<?php
require_once "utils/GoBack.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add item</title>
    <base href="<?= $web_root ?>">
    <meta name="author" content="Jonathan_Vanderbiest">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="css/styles.css" rel="stylesheet">
    <script src="lib/jquery-4.0.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="scripts/get_constants.js"></script>
    <script type="module" src="scripts/add_edit_item.js"></script>
</head>

<body style="padding-top:60px; padding-bottom: 140px;">
    <?php
    $page_title = ($item->get_id() > 0) ? 'Edit Item' : 'Add Item';
    $hide_back_button = ($item->get_id() > 0) ? false : true;


    // On cache l'icône "panier" pour cette page
    $hide_default_icon = true;

    // On utilise ob_start() pour capturer le HTML proprement, ou juste une stringgit
    ob_start();
    ?>
    <button type="submit" id="submitBtn" form="itemForm" class="btn-icon text-primary">
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
            <input type="hidden" name="context" value="<?= $context ?? 'from-browse' ?>">
            <input type="hidden" name="encoded_search" value="<?= $encoded_search ?? '' ?>">
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
                            class="form-control bg-black bg-opacity-25 text-white 
                            <?= isset($errors['title']) || isset($errors['empty']) ? 'is-invalid' : 'border-secondary' ?>"
                            name="title"
                            id="title"
                            placeholder="Ex: IPhone 13 Pro Max 256GB"
                            value="<?php echo html_entity_decode($item->get_title()) ?>">
                        <div id="errTitle" class="text-danger small mt-1">
                            <?php if (isset($errors['empty'])): ?>
                                <?php echo ($errors['empty']); ?>
                            <?php elseif (isset($errors['title'])): ?>
                                <?php echo ($errors['title']); ?>
                            <?php endif; ?>
                        </div>
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
                            class="form-control bg-black bg-opacity-25 text-white <?= isset($errors['duration']) ? 'is-invalid' : 'border-secondary' ?>"
                            id="duration"
                            name="duration"
                            placeholder="e.g, 1"
                            value="<?php echo $item->get_duration_days() ?>">
                        <div id="errDuration" class="text-danger small mt-1">
                            <?php if (isset($errors['duration'])) { ?>
                                <?php echo $errors['duration'] ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class=" container mb-4">
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
                                        class="form-control bg-black bg-opacity-25 text-white <?php echo (isset($errors['starting_bid']) || isset($errors['sale']) || isset($errors['sale_price'])) ? 'is-invalid' : 'border-secondary' ?>"
                                        id="starting_bid"
                                        name="starting_bid"
                                        placeholder="e.g., 50.00"
                                        value="<?php echo ($item->get_starting_bid() > 0) ? $item->get_starting_bid() : '' ?>">
                                    <span class="input-group-text bg-dark text-white border-secondary">€</span>
                                </div>
                                <div id="errStartingBid" class="text-danger small mt-1">
                                    <?php if (isset($errors['starting_bid'])) { ?>
                                        <?php echo $errors['starting_bid'] ?>
                                    <?php } ?>
                                    <?php if (isset($errors['sale'])) { ?>
                                        <?php echo $errors['sale'] ?>
                                    <?php } ?>
                                    <?php if (isset($errors['sale_price'])) { ?>
                                        <?php echo $errors['sale_price'] ?>
                                    <?php } ?>
                                </div>

                                <div class="mb-3">
                                    <label for="instant" class="form-label fw-bold text-white">Instant Purchase Price (optional)</label>
                                    <div class="input-group">
                                        <input type="number"
                                            class="form-control bg-black bg-opacity-25 text-white <?php echo isset($errors['buy_now_price']) ? 'is-invalid' : 'border-secondary' ?>"
                                            id="instant"
                                            name="instant"
                                            placeholder="e.g., 200.00"
                                            value="<?php echo ($item->get_has_buy_now() && $item->get_is_auction() && $item->get_buy_now_price() > 0) ? $item->get_buy_now_price() : '' ?>">

                                        <span class="input-group-text bg-dark text-white border-secondary">€</span>
                                    </div>

                                    <div id="errInstant" class="text-danger small mt-1">
                                        <?php if (isset($errors['buy_now_price'])): ?>
                                            <?php echo $errors['buy_now_price'] ?>
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
                                            class="form-control bg-black bg-opacity-25 text-white  <?php echo isset($errors['sale_price']) ? 'is-invalid' : 'border-secondary' ?>"
                                            id="direct"
                                            name="direct"
                                            placeholder="e.g., 400.00"
                                            value="<?php echo isset($_POST['direct']) ? $_POST['direct'] : (($item->get_is_direct_sale() && $item->get_buy_now_price() > 0) ? $item->get_buy_now_price() : '') ?>">
                                        <span class="input-group-text bg-dark text-white border-secondary">€</span>

                                    </div>
                                    <div id="errDirect" class="text-danger small mt-1">
                                        <?php if (isset($errors['sale_price'])): ?>
                                            <?php echo $errors['sale_price'] ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class=" container mb-4">
            <div class="card text-bg-dark border-secondary">
                <div class="card-header border-secondary" style="background-color: #1e293b;">
                    <h5 class="mb-0 text-white text-start">Categories</h5>
                </div>

                <div class="p-3 bg-dark">
                    <p>You can select up to 3 categories.</p>

                    <?php foreach ($categories as $category): ?>
                        <div class="form-check">
                            <input class="form-check-input bg-dark"
                                type="checkbox"
                                name="categories[]"
                                id="category_<?= $category->getId(); ?>"
                                value="<?= $category->getId(); ?>">

                            <label class="form-check-label text-white fs-4" for="category_<?= $category->getId(); ?>">
                                <?= htmlspecialchars($category->getName()); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </form>

    <footer class="fixed-bottom">
        <?php include 'view/view_timer.php'; ?>
        <?php include 'view/view_footer.php'; ?>
    </footer>

    <div class="modal fade" id="unsavedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white shadow-lg" style="border: 1px solid #444;">

                <div class="modal-header border-bottom align-items-center" style="border-bottom-color: #444 !important;">
                    <h5 class=" modal-title fs-2 fw-semibold">Unsaved changes</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-4">
                    <p class="m-0 text-white">You have unsaved changes. Leave anyway?</p>
                </div>

                <div class="modal-footer border-top d-flex justify-content-end gap-2" style="border-top-color: #444 !important;">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" id="confirmLeaveBtn">Leave</button>
                </div>

            </div>
        </div>
    </div>
</body>

</html>