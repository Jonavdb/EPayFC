<?php require_once "utils/GoBack.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <base href="<?= $web_root ?>">
    <meta name="author" content="HABOULA_Ali">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
    <main class="container" style="padding-top: 80px; padding-bottom: 100px;">
        <?php if (isset($user) && is_object($user)): ?>
        <?php else: ?>
            <p>Utilisateur non trouvé.</p>
        <?php endif; ?>

        <!-- HEADER -->
        <?php
        $page_title = "Profile";
        $hide_back_button = true;
        include 'view/view_header.php';
        ?>

        <!-- PROFILE HEADER -->
        <div class="card mb-4 bg-dark shadow-soft">
            <div class="card-body text-center py-2">
                <div class="mb-3">
                    <!-- Setup d'une image par défaut si l'user n'a pas d'avatar -->
                    <?php
                    $avatarPath = "css/images/default_user_image.jpg"; // Image par défaut

                    if (!empty($user->get_picture_path()) && file_exists($user->get_picture_path())) {
                        $avatarPath = $user->get_picture_path();
                    }
                    ?>

                    <div class="mx-auto" style="width: 150px; height: 150px">
                        <img src="<?= $avatarPath ?>"
                            alt="Profile Picture"
                            class="rounded-circle border-secondary p-1 img-thumbnail object-fit-cover w-100 h-100">
                    </div>
                </div>

                <h1 class="text-white mb-1 fs-2">
                    <?= $user->get_full_name() ?>
                </h1>

                <p class="card-profile-header-text fs-4 mb-1">
                    @<?= $user->get_pseudo() ?>
                </p>

                <p class="card-profile-header-text fs-4">
                    <?= $user->get_email() ?>
                </p>
            </div>
        </div>

        <!-- MY ACTIVITIES -->
        <div class="card bg-dark border-secondary mb-4">
            <div class="card-header border-secondary text-white fw-semibold card-profile-header-bg">
                My Activities
            </div>

            <div class="card-body p-4">
                <a href="item/sales" class="text-decoration-none">
                    <div class="d-flex align-items-center p-2 mb-3 border border-primary rounded hover-effect">
                        <div class="me-3 p-2">
                            <i class="fa-solid fa-file-invoice fa-lg text-primary"></i>
                        </div>
                        <div class="text-lg-start ms-3">
                            <p class="m-0 fs-4 text-md text-primary fw-bold">Sales</p>
                            <p class="m-0 border-color">Items you have sold</p>
                        </div>
                    </div>
                </a>

                <a href="item/purchases" class="text-decoration-none">
                    <div class="d-flex align-items-center p-2 border border-primary rounded hover-effect">
                        <div class="me-4 p-2">
                            <i class="fa-solid fa-cart-shopping fa-lg text-primary"></i>
                        </div>
                        <div class="text-lg-start">
                            <p class="m-0 fs-4 text-md text-primary fw-bold">Purchases</p>
                            <p class="m-0 border-color">Items you have purchased</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- ACCOUNT SETTINGS -->
        <div class="card mb-4 bg-dark border-secondary">
            <div class="card-header border-secondary text-white fw-semibold card-profile-header-bg">
                Account Settings
            </div>

            <div class="card-body p-4">
                <a href="user/edit_profile" class="text-decoration-none">
                    <div class="d-flex align-items-center p-2 mb-3 border border-primary rounded hover-effect">
                        <div class="me-1 p-2">
                            <i class="fa-solid fa-user-pen fa-lg text-primary"></i>
                        </div>
                        <div class="text-lg-start ms-3">
                            <p class="m-0 fs-4 text-md text-primary fw-bold">Edit Profile</p>
                            <p class="m-0 border-color">Update your personal information</p>
                        </div>
                    </div>
                </a>

                <a href="user/change_password" class="text-decoration-none">
                    <div class="d-flex align-items-center p-2 mb-3 border border-primary rounded hover-effect">
                        <div class="me-3 p-2">
                            <i class="fa-solid fa-lock fa-lg text-primary"></i>
                        </div>
                        <div class="text-lg-start ms-3">
                            <p class="m-0 fs-4 text-md text-primary fw-bold">Change Password</p>
                            <p class="m-0 border-color">Update your account security</p>
                        </div>
                    </div>
                </a>

                <a href="user/manage_profile_picture" class="text-decoration-none">
                    <div class="d-flex align-items-center p-2 mb-3 border border-primary rounded hover-effect">
                        <div class="me-3 p-2">
                            <i class="fa-solid fa-circle-user fa-lg text-primary"></i>
                        </div>
                        <div class="text-lg-start ms-3">
                            <p class="m-0 fs-4 text-md text-primary fw-bold">Profile Picture</p>
                            <p class="m-0 border-color">Upload or change your profile picture</p>
                        </div>
                    </div>
                </a>

                <a href="user/logout" class="text-decoration-none">
                    <div class="d-flex align-items-center p-2 border border-danger rounded hover-effect-danger">
                        <div class="me-3 p-2">
                            <i class="fa-solid fa-right-from-bracket fa-lg text-danger"></i>
                        </div>
                        <div class="text-lg-start ms-3">
                            <p class="m-0 fs-4 text-md text-danger fw-bold">Logout</p>
                            <p class="m-0 border-color">Sign out of your account</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="fixed-bottom">
        <?php include 'view/view_footer.php'; ?>
    </footer>
</body>

</html>