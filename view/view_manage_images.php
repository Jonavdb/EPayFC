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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.14.2/jquery-ui.js"></script>
    <script src="jquery.ui.touch-punch.min.js"></script>
    <title>Manage Images</title>
</head>

<body class="d-block h-auto pt-2 pb-5">

    <?php
    $page_title = "Manage Images";
    include 'view/view_header.php';
    ?>

    <div class="container-fluid mb-4 px-5" style="margin-top: 80px;">
        <h4 class="text-white text-start">
            Manage Images for "<?= $item->get_title() ?>"
        </h4>
        <?php if (isset($errors)): ?>
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    <div class="container-fluid mb-4 px-5">
        <div class="card text-bg-dark border-secondary">
            <div class="card-header border-secondary" style="background-color: #1e293b;">
                <h5 class="mb-0 text-white text-start fs-3">Add new Images</h5>
            </div>
            <div class="card-body">
                <h6 class="fw-bold text-white text-start fs-4">Select Images</h6>
                <form action="item/add_images" method="POST" enctype="multipart/form-data" class="fs-5">

                    <input type="hidden" name="item_id" value="<?= $item->get_id() ?>">

                    <input class="form-control bg-dark text-secondary border-secondary"
                        type="file"
                        name="image_files[]"
                        accept="image/png, image/jpeg, image/webp, image/gif"
                        multiple>
                    <div class="small text-secondary mt-2">
                        You can select multiple images (JPG, PNG, GIF, WEBP).Images will be added to the end of the current list.
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary fs-5">
                            Upload Images
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="container-fluid mb-4 px-5">
        <div class="card text-bg-dark border-secondary">
            <div class="card-header border-secondary" style="background-color: #1e293b;">
                <h5 class="mb-0 text-white text-start fs-4">Current Images</h5>
            </div>
            <div class="card-body">
                <?php if (empty($item->get_image_urls())) { ?>
                    <p class="text-secondary m-0">No images uploaded yet.</p>
                <?php } else { ?>
                    <div class="d-flex flex-wrap gap-3" id="sortable-images">

                        <?php
                        // On compte les images pour savoir quand arrêter le bouton "droite"
                        $total_images = count($item->get_image_urls());

                        foreach ($item->get_image_urls() as $index => $image_url):
                        ?>

                            <div class="card border-secondary bg-dark" style="width: 220px; " data-image-url="<?= basename($image_url) ?>"> <!-- ajout de basename afin de sécuriser le tout et ne garder que le nom du fichier -->

                                <div class="bg-white rounded-top" style="height: 150px;">
                                    <img src="<?= $image_url ?>"
                                        class="w-100 h-100"
                                        style="object-fit: contain;"
                                        alt="Image">
                                </div>

                                <div class="card-body d-flex justify-content-between align-items-center gap-3">
                                    <form action="item/move_image_left" method="POST" class="m-0 p-0 border-0 bg-transparent shadow-lg">
                                        <input type="hidden" name="item_id" value="<?= $item->get_id() ?>">
                                        <input type="hidden" name="image_index" value="<?= $index ?>">

                                        <button type="submit" class="btn btn-sm btn-outline-secondary" <?= $index === 0 ? 'disabled' : '' ?>>
                                            <i class="fa-solid fa-arrow-left"></i>
                                        </button>
                                    </form>

                                    <form action="item/move_image_right" method="POST" class="m-0 p-0 border-0 bg-transparent shadow-lg">
                                        <input type="hidden" name="item_id" value="<?= $item->get_id() ?>">
                                        <input type="hidden" name="image_index" value="<?= $index ?>">

                                        <button type="submit" class="btn btn-sm btn-outline-secondary" <?= $index === ($total_images - 1) ? 'disabled' : '' ?>>
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </button>
                                    </form>

                                    <form action="item/delete_images" method="POST" class="m-0 p-0 border-0 bg-transparent shadow-lg">
                                        <input type="hidden" name="item_id" value="<?= $item->get_id() ?>">
                                        <input type="hidden" name="image_filename" value="<?= basename($image_url) ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="fixed-bottom">
        <?php include 'view/view_footer.php'; ?>
    </footer>
    <script>
        $(document).ready(function() {
            $('#sortable-images').sortable({ // on rend les images sortables avec jQueryUI
                stop: function() { // quand l'image a été lachée, on déclenche la fonction
                    const ordre = $('#sortable-images').sortable('toArray', {
                        attribute: 'data-image-url'
                    });

                    $.post('item/update_image_order', { // envoie une requête POST sur le controlleur item et la méthode update_image_order en lui passant 2 paramètres
                        item_id: <?= $item->get_id() ?>,
                        order: ordre
                    });
                }
            })
        })
    </script>
</body>

</html>