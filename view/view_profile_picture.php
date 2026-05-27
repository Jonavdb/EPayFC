<?php require_once "utils/GoBack.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
    <meta name="author" content="Jonathan_Vanderbiest">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <title>Manage profile picture</title>
</head>

<body class="d-block h-auto pt-2 pb-5">
    <?php
    $page_title = "Manage Profile Picture";
    include 'view/view_header.php';
    ?>

    <div class="container-fluid mb-4 px-5" style="margin-top: 60px;">
        <?php if (isset($errors)): ?>
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="card text-bg-dark border-secondary">
            <div class="card-header border-secondary" style="background-color: #1e293b;">
                <h1 class="mb-0 text-white text-start fs-5">Current Profile Picture</h1>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="mx-auto" style="width: 120px; height: 120px">
                        <img src="<?= !empty($user->get_picture_path()) ? $user->get_picture_path() : 'css/images/default_user_image.jpg' ?>"
                            alt="Profile Picture"
                            class="rounded-circle border-secondary p-1 img-thumbnail object-fit-cover w-100 h-100">
                    </div>
                    <h1 class="mt-3 fs-2 mb-0 text-white fs-5"><?= htmlspecialchars($user->get_pseudo()) ?></h1>
                    <p class="text-secondary small"><?= htmlspecialchars($user->get_email()) ?></p>
                </div>
            </div>
        </div>

    </div>

    <div class="container-fluid mb-4 px-5">
        <div class="card text-bg-dark border-secondary">
            <div class="card-header border-secondary" style="background-color: #1e293b;">
                <h1 class="mb-0 text-white text-start fs-5">Upload New Picture</h1>
            </div>
            <div class="card-body">
                <h1 class="fw-bold text-white text-start fs-5">Select Image</h1>
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

    if (!empty($user->get_picture_path())) { ?>

        <div class="container-fluid mb-4 px-5">
            <div class="card text-bg-dark border-secondary">
                <div class="card-header border-secondary" style="background-color: #1e293b;">
                    <h1 class="mb-0 text-white text-start fs-5">Remove Current Picture </h1>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning border-secondary text-start">
                        This will permanentely delete your profile picture. You can upload a new one anytime.
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