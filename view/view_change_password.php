<?php require_once "utils/GoBack.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
    <main class="container">
        <!-- HEADER -->
        <?php
        ob_start();
        ?>
        <button type="submit" form="passwordForm" class="btn-icon text-primary">
            <i class="fa-regular fa-floppy-disk fa-lg"></i>
        </button>

        <?php
        $page_title = "Change password";
        $header_right_content = ob_get_clean();
        include 'view/view_header.php';
        ?>

        <form id="passwordForm" action="user/change_password" method="post">
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
                            placeholder="Enter your current password"
                            value="<?= htmlspecialchars($current_password); ?>">

                        <?php if (isset($errors['current_password'])): ?>
                            <ul class="text-danger small mt-1 mb-0 ps-4">
                                <li><?= $errors['current_password']; ?></li>
                            </ul>
                        <?php endif; ?>

                        <div class="form-text text-secondary">
                            Enter your current password to verify your identity
                        </div>
                    </div>
                </div>
            </div>

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
                            placeholder="Enter your new password"
                            value="<?= htmlspecialchars($new_password); ?>">

                        <?php if (isset($errors['password_length']) || isset($errors['password_retry'])): ?>
                            <ul class="text-danger small mt-1 mb-0 ps-4">
                                <?php if (isset($errors['password_length'])): ?>
                                    <li><?= $errors['password_length']; ?></li>
                                <?php endif; ?>
                                <?php if (isset($errors['password_retry'])): ?>
                                    <li><?= $errors['password_retry']; ?></li>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>

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
                            value="<?= htmlspecialchars($confirm_password); ?>"
                            placeholder="Confirm your new password">

                        <?php if (isset($errors['same_password'])): ?>
                            <ul class="text-danger small mt-1 mb-0 ps-4">
                                <li><?= $errors['same_password']; ?></li>
                            </ul>
                        <?php endif; ?>

                        <div class="form-text text-secondary">
                            Re-enter your new password to confirm it matches
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <!-- FOOTER -->
    <footer class="fixed-bottom">
        <?php include 'view/view_footer.php'; ?>
    </footer>
</body>

</html>