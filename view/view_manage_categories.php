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
    <script src=" lib/jquery-4.0.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="scripts/manage_categories.js"></script>
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
        <div id="category-manager" class="row justify-content-center sticky-top">
            <div class="col-11">
                <div class="card shadow bg-dark">
                    <div class="card-body px-4">

                        <div id="sortable-category-list">
                            <?php foreach ($categories as $index => $category): ?>

                                <form action="category/handle_action" method="POST" data-id="<?= $category->get_id() ?>" class="border-0 shadow-none m-0 p-0 category-row">
                                    <input type="hidden" name="id" value="<?= $category->get_id() ?>">

                                    <div class="mb-4">
                                        <div class="row align-items-center">

                                            <div class="col-8 no-js-element">
                                                <input type="text"
                                                    class="form-control form-control-lg bg-dark border-secondary text-white fs-4"
                                                    name="name"
                                                    value="<?= $category->get_name() ?>"
                                                    placeholder="Category Name">
                                            </div>

                                            <div class="col-4 d-flex gap-3 no-js-element">
                                                <button type="submit" name="action" value="up" class="btn btn-lg border border-success btn-outline-success fs-4 flex-fill" <?= ($index === 0) ? 'disabled' : '' ?>>Up</button>
                                                <button type="submit" name="action" value="down" class="btn btn-lg border border-success btn-outline-success fs-4 flex-fill" <?= ($index === count($categories) - 1) ? 'disabled' : '' ?>>Down</button>
                                                <button type="submit" name="action" value="save" class="btn btn-lg border border-success btn-outline-success fs-4 flex-fill">Save</button>
                                                <button type="submit" name="action" value="delete" class="btn btn-lg border border-danger btn-outline-danger fs-4 flex-fill" <?= !($categories[$index]->is_empty()) ? 'disabled' : '' ?>>Delete</button>
                                            </div>

                                            <div class="col-10 js-element d-none js-edit-input">
                                                <input type="text"
                                                    class="form-control form-control-lg bg-dark border-secondary text-white fs-4"
                                                    name="name"
                                                    value="<?= $category->get_name() ?>"
                                                    placeholder="Category Name">
                                            </div>

                                            <div class="col-2 d-flex gap-3 js-element d-none js-edit-input">
                                                <button type="button" data-id="<?= $category->get_id() ?>" class="btn btn-lg border border-success btn-outline-success fs-4 edit-btn">Edit</button>
                                                <button type="button" data-id="<?= $category->get_id() ?>" class="btn btn-lg border border-danger btn-outline-danger fs-4 delete-btn" <?= !($categories[$index]->is_empty()) ? 'disabled' : '' ?>>Delete</button>
                                            </div>

                                            <div class="col-10 d-flex flex-row gap-3 js-element d-none js-read-display">
                                                <div class="drag-handle" style="cursor: grab;">
                                                    <i class="fa-solid fa-grip-vertical text-secondary text-opacity-50"></i>
                                                </div>
                                                <div class="fs-4 text-white"><?= $category->get_name() . " (" . $category->get_items_count() . ")" ?></div>
                                            </div>

                                            <div class="col-2 d-flex gap-3 js-element d-none js-read-display">
                                                <button type="button" data-id="<?= $category->get_id() ?>" class="btn btn-lg border border-success btn-outline-success fs-4 edit-btn">Edit</button>
                                                <button type="button" data-id="<?= $category->get_id() ?>" class="btn btn-lg border border-danger btn-outline-danger fs-4 delete-btn" <?= !($categories[$index]->is_empty()) ? 'disabled' : '' ?>>Delete</button>
                                            </div>

                                        </div>

                                        <?php if (isset($save_errors) && !empty($save_errors) && $category->get_id() === $target_id): ?>
                                            <div class="row">
                                                <div class="col-8">
                                                    <div class="errSave w-100">
                                                        <ul>
                                                            <?php foreach ($save_errors as $error): ?>
                                                                <li class="text-danger small mt-1"><?= $error ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="col-4"></div>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </form>
                            <?php endforeach; ?>

                            <hr class="border-secondary opacity-25" />

                            <form action="category/add_category" method="POST" class="border-0 m-0 p-0">
                                <div class="row align-items-center mt-2">
                                    <div class="col-12 d-flex gap-4">
                                        <input type="text" name="name" id="categoryInput"
                                            class="form-control form-control-lg bg-dark border-secondary text-white fs-4 flex-grow-1 js-hide-on-load"
                                            placeholder="Category Name">

                                        <button type="submit" id="addCategoryBtn"
                                            class="btn btn-lg btn-primary px-4 d-flex align-items-center justify-content-center fw-bold fs-3 ms-auto no-js-element">+</button>

                                        <button type="button" id="cancelAddBtn"
                                            class="btn btn-lg btn-danger px-4 d-flex align-items-center justify-content-center fw-bold fs-3 ms-auto cancel-btn js-hide-on-load">Cancel</button>

                                        <button type="button" id="addCategoryBtn"
                                            class="btn btn-lg btn-primary px-4 d-flex align-items-center justify-content-center fw-bold fs-3 ms-auto add-btn js-element">+</button>


                                    </div>
                                </div>
                            </form>

                            <div id="errInsert" class="text-danger small mt-1">
                                <ul>
                                    <?php if (isset($insert_errors) && !empty($insert_errors)): ?>
                                        <?php foreach ($insert_errors as $error): ?>
                                            <li> <?= $error ?></li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </main>
    <!-- FOOTER -->
    <footer class="fixed-bottom">
        <?php $is_guest = false ?> <!-- debug -->
        <?php include 'view/view_timer.php'; ?>
        <?php include 'view/view_footer.php'; ?>

    </footer>

    <!-- Modale Unsave -->
    <div class="modal fade" id="unsavedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white shadow-lg" style="border: 1px solid #444;">

                <div class="modal-header border-bottom align-items-center" style="border-bottom-color: #444 !important;">
                    <h5 class=" modal-title fs-2 fw-semibold">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-4">
                    <p class="m-0 text-white">Confirm category deletion ?</p>
                </div>

                <div class="modal-footer border-top d-flex justify-content-end gap-2" style="border-top-color: #444 !important;">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary px-4" id="confirmLeaveBtn">Yes</button>
                </div>

            </div>
        </div>
    </div>
</body>

</html>