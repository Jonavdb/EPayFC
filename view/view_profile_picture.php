<?php require_once "utils/GoBack.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
    <meta name="author" content="Jonathan_Vanderbiest">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <title>Manage profile picture</title>
</head>

<body class="d-block h-auto pt-2 pb-5">
    <!-- HEADER (pas include car le go_back est différent) -->
    <header class=" content-box">
        <nav class="navbar fixed-top navbar-dark bg-dark border-bottom shadow-lg">
            <div class="container-fluid p-2">

                <div class="d-flex align-items-center" style="min-width: 50px;">
                    <a href="<?= GoBack::go_back_2() ?>" class="btn-icon">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>

                <div class="flex-grow-1 text-center mx-auto">
                    <div class="fw-bold fs-2 text-primary">
                        Manage Profile picture

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

    <div class="container-fluid mb-4 px-5" style="margin-top: 60px;">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <div class="card text-bg-dark border-secondary">
            <div class="card-header border-secondary" style="background-color: #1e293b;">
                <h5 class="mb-0 text-white text-start">Current Profile Picture</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="mx-auto" style="width: 120px; height: 120px">
                        <img src="<?= !empty($user->picture_path) ? $user->picture_path : 'css/images/default_user_image.jpg' ?>"
                            alt="Profile Picture"
                            class="rounded-circle border-secondary p-1 img-thumbnail object-fit-cover w-100 h-100">
                    </div>
                    <h5 class="mt-3 fs-2 mb-0 text-white"><?= htmlspecialchars($user->pseudo) ?></h5>
                    <p class="text-secondary small"><?= htmlspecialchars($user->email) ?></p>
                </div>
            </div>
        </div>

    </div>

    <div class="container-fluid mb-4 px-5">
        <div class="card text-bg-dark border-secondary">
            <div class="card-header border-secondary" style="background-color: #1e293b;">
                <h5 class="mb-0 text-white text-start">Upload New Picture</h5>
            </div>
            <div class="card-body">
                <h6 class="fw-bold text-white text-start">Select Image</h6>
                <form action="user/update_profile_picture" method="POST" enctype="multipart/form-data">
                    <input class="form-control bg-dark text-secondary border-secondary"
                        type="file"
                        name="profile_pic"
                        accept="image/png, image/jpeg, image/webp, image/gif">
                    <div class="small text-secondary mt-2">
                        Supported formats: JPG, PNG, GIF, WebP. This will replace your current profile picture.
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            Upload picture
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <?php

    if (!empty($user->picture_path)) { ?>

        <div class="container-fluid mb-4 px-5">
            <div class="card text-bg-dark border-secondary">
                <div class="card-header border-secondary" style="background-color: #1e293b;">
                    <h5 class="mb-0 text-white text-start">Remove Current Picture </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning border-secondary text-start">

                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        This will permanentely delete your profile picture.
                    </div>
                    <form action="user/delete_profile_picture" method="POST" class="text-start mt-3">
                        <button type="submit" class="btn btn-danger">
                            Delete Picture
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>

    <footer class="fixed-bottom">
        <?php include 'view/view_footer.php'; ?>
    </footer>
</body>

</html>