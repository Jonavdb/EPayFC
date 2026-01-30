<?php require_once "utils/GoBack.php"; ?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <main class="container">
        <!-- HEADER -->
        <header class="content-box">
            <nav class="navbar fixed-top navbar-dark bg-dark border-top shadow-lg">
                <div class="container-fluid">
                    <div class="flex-grow-1 text-start" style="max-width: 5%;">
                        <a href="<?= GoBack::go_back() ?>" class="btn-icon">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                    </div>

                    <div class="mx-auto">
                        <div class="fw-bold fs-2 text-primary">
                            Change password <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                    </div>

                    <button type="submit" form="passwordForm" class="btn-icon">
                        <i class="fa-regular fa-floppy-disk fa-lg"></i>
                    </button>
                </div>
            </nav>
        </header>

        <form id="passwordForm" action="user/change_password" method="post">
            <!-- CURRENT PASSWORD -->
            <div class="card mb-4 bg-dark border-secondary">
                <div class="card-header border-secondary text-white fw-semibold card-header-bg">
                    Current Password
                </div>

                <div class="card-body">
                    <div class="mb-1">
                        <label for="current_password" class="form-label text-white fw-bold small">Current Password *</label>
                        <input type="password"
                            name="current_password"
                            class="form-control bg-dark border-secondary text-white"
                            id="current_password"
                            placeholder="Enter your current password">

                        <div class="form-text text-secondary">
                            Enter your current password to verify your identity
                        </div>
                    </div>
                </div>
            </div>

            <!-- NEW PASSWORD -->
            <div class="card mb-4 bg-dark border-secondary">
                <div class="card-header border-secondary text-white fw-semibold card-header-bg">
                    New Password
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label for="new_password" class="form-label text-white fw-bold small">New Password *</label>
                        <input type="password"
                            name="new_password"
                            class="form-control bg-dark border-secondary text-white"
                            id="new_password"
                            placeholder="Enter your new password">

                        <div class="form-text text-secondary">
                            Password must be 8-16 characters with uppercase, number, and punctuation
                        </div>
                    </div>

                    <div class="mb-1">
                        <label for="new_password_confirm" class="form-label text-white fw-bold small">Confirm New Password *</label>
                        <input type="password"
                            name="new_password_confirm"
                            class="form-control bg-dark border-secondary text-white"
                            id="new_password_confirm"
                            placeholder="Confirm your new password">

                        <div class="form-text text-secondary">
                            Re-enter your new password to confirm it matches
                        </div>
                    </div>
                </div>
            </div>

            <?php if (count($errors) != 0): ?>
                <div class='errors'>
                    <p>Please correct the following error(s) :</p>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </form>
    </main>
    <!-- FOOTER -->
    <footer class="content-box">
        <nav class="navbar fixed-bottom navbar-dark bg-dark border-top shadow-lg">
            <div class="container-fluid">

                <div class="d-flex justify-content-around w-100 py-1">

                    <a class="nav-link text-center text-secondary" href="item/browse">
                        <i class="fa-solid fa-magnifying-glass-plus fs-5 mb-1 d-block"></i>
                        <span>Browse</span>
                    </a>

                    <a class="nav-link text-center text-secondary" href="item/list">
                        <i class="fas fa-home fs-5 mb-1 d-block"></i>
                        <span>My Items</span>
                    </a>

                    <a class="nav-link text-center text-secondary" href="item/add">
                        <i class="fa-solid fa-plus fs-5 mb-1 d-block"></i>
                        <span>Add Offer</span>
                    </a>

                    <a class="nav-link text-center text-secondary" href="user/profile">
                        <i class="fa-solid fa-gear fs-5 mb-1 d-block"></i>
                        <span>Profile</span>
                    </a>
                </div>
            </div>
        </nav>
    </footer>
</body>

</html>