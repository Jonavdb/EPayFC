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
        <div class="d-flex justify-content-between align-items-center p-3 mb-3 nav-header text-white rounded">
            <a href="user/profile" class="btn-icon">
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <h4 class="m-0 text-primary fw-bold">
                Change password <i class="fa-solid fa-cart-shopping"></i>
            </h4>

            <!-- BTN SUBMIT -->
            <button type="submit" form="passwordForm" class="btn-icon">
                <i class="fa-regular fa-floppy-disk fa-lg"></i>
            </button>
        </div>

        <form id="passwordForm" action="user/change_password" method="post">
            <!-- CURRENT PASSWORD -->
            <div class="card mb-4 bg-dark border-secondary">
                <div class="card-header border-secondary text-white fw-semibold card-header-bg">
                    Current Password
                </div>

                <div class="card-body">
                    <div class="mb-1">
                        <label for="currentPassword" class="form-label text-white fw-bold small">Current Password *</label>
                        <input type="password"
                            name="current_password"
                            class="form-control bg-dark border-secondary text-white"
                            id="currentPassword"
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
        </form>
    </main>
</body>

</html>