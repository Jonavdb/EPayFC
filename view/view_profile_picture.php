<?php
require_once "utils/GoBack.php";
require_once "utils/AppTime.php";
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
    <title>Manage images</title>
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
        <div class="container-fluid d-flex align-items-center mb-4 px-5">
            <div class="left-zone">
                <a href="<?= GoBack::go_back() ?>" class="btn-icon">
                    <i class="fa-solid fa-arrow-left" style="color: #2D68C4; font-size: 22px;"></i>
                </a>
            </div>
            <div class="flex-grow-1 text-center">
                <h2 style="color: #2D68C4; margin: 0;"> Manage Profile picture
                </h2>
            </div>
        </div>
    </nav>

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
                    <div class="position-relative d-inline-block">
                        <img src="<?= !empty($user->picture_path) ? $user->picture_path : 'uploads/users/avatar_default.jpg' ?>"
                            alt="Profile Picture"
                            class="rounded-circle border border-secondary"
                            style="width: 100px; height: 100px; object-fit: cover">
                    </div>
                    <h5 class="mt-2 mb-0 text-white"><?= htmlspecialchars($user->pseudo) ?></h5>
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

    <div class="fixed-bottom">
        <nav class="navbar navbar-dark bg-dark">
            <ul class="navbar-nav bottom-nav" id="nav-icons">
                <li class="nav-item text-center">
                    <a href="browseItems/index" class="nav-link"><i class="fa-solid fa-magnifying-glass d-block"></i><small style="font-size: 10px;">Browse</small></a>
                </li>
                <li class="nav-item text-center">
                    <a href="item/list" class="nav-link"><i class="fa-solid fa-house d-block"></i><small style="font-size: 10px;">My Items</small></a>
                </li>
                <li class="nav-item text-center">
                    <a href="item/add_edit_item" class="nav-link"><i class="fa-solid fa-plus d-block"></i><small style="font-size: 10px;">Add Offer</small></a>
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