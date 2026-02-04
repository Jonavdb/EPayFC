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
    <link rel="stylesheet" href="css/styles.css">
    <title>Manage Images</title>
</head>

<body class="d-block h-auto pt-2 pb-5">

    <!-- HEADER (pas include car le go_back est différent) -->
    <header class="content-box">
        <nav class="navbar fixed-top navbar-dark bg-dark border-bottom shadow-lg">
            <div class="container-fluid p-2">

                <div class="d-flex align-items-center" style="min-width: 50px;">
                    <a href="<?= GoBack::go_back_2() ?>" class="btn-icon">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>

                <div class="flex-grow-1 text-center mx-auto">
                    <div class="fw-bold fs-2 text-primary">
                        Manage Images

                        <?php if (!isset($hide_default_icon) || !$hide_default_icon): ?>
                            <i class="fa fa-shopping-cart ms-2"></i>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-center" style="min-width: 50px;">
                    <?= $header_right_content ?? '' ?>
                </div>
            </div>
        </nav>
    </header>

    <div class="container-fluid mb-4 px-5" style="margin-top: 80px;">
        <h4 class="text-white text-start">
            Manage Images for "<?= $item->title ?>"
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

                    <input type="hidden" name="item_id" value="<?= $item->id ?>">

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
                <?php if (empty($item->image_urls)) { ?>
                    <p class="text-secondary m-0">No images uploaded yet.</p>
                <?php } else { ?>
                    <div class="d-flex flex-wrap gap-3">

                        <?php
                        // On compte les images pour savoir quand arrêter le bouton "droite"
                        $total_images = count($item->image_urls);

                        foreach ($item->image_urls as $index => $image_url):
                        ?>

                            <div class="card border-secondary bg-dark" style="width: 220px;">

                                <div class="bg-white rounded-top" style="height: 150px;">
                                    <img src="<?= $image_url ?>"
                                        class="w-100 h-100"
                                        style="object-fit: contain;"
                                        alt="Image">
                                </div>

                                <div class="card-body d-flex justify-content-between align-items-center gap-3">
                                    <form action="index.php?controller=item&action=move_image_left" method="POST" class="m-0 p-0 border-0 bg-transparent shadow-lg">
                                        <input type="hidden" name="item_id" value="<?= $item->id ?>">
                                        <input type="hidden" name="image_index" value="<?= $index ?>">

                                        <button type="submit" class="btn btn-sm btn-outline-secondary" <?= $index === 0 ? 'disabled' : '' ?>>
                                            <i class="fa-solid fa-arrow-left"></i>
                                        </button>
                                    </form>

                                    <form action="index.php?controller=item&action=move_image_right" method="POST" class="m-0 p-0 border-0 bg-transparent shadow-lg">
                                        <input type="hidden" name="item_id" value="<?= $item->id ?>">
                                        <input type="hidden" name="image_index" value="<?= $index ?>">

                                        <button type="submit" class="btn btn-sm btn-outline-secondary" <?= $index === ($total_images - 1) ? 'disabled' : '' ?>>
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </button>
                                    </form>

                                    <form action="index.php?controller=item&action=delete_images" method="POST" class="m-0 p-0 border-0 bg-transparent shadow-lg">
                                        <input type="hidden" name="item_id" value="<?= $item->id ?>">
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
</body>

</html>