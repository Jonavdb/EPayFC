<?php
require_once "utils/GoBack.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign up</title>
    <base href="<?= $web_root ?>">
    <meta name="author" content="HABOULA_Ali">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <script src="lib/jquery-4.0.0.min.js" type="text/javascript"></script>
    <script type="module" src="scripts/signup.js"></script>
</head>

<body>
    <main>
        <form id="signupForm" action="user/signup" method="post">
            <h2 class="text-center text-white">Sign up</h2>
            <span class="divider"></span>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-light">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control bg-dark border-secondary text-light"
                        value="<?= htmlspecialchars($email); ?>"
                        placeholder="Mail">
                </div>
                <div id="errEmail" class="text-danger small mt-1 mb-0 ps-4">
                    <?php if (isset($errors['email_format']) || isset($errors['email_already_used'])): ?>
                        <?php if (isset($errors['email_format'])): ?>
                            <li><?= $errors['email_format']; ?></li>
                        <?php endif; ?>
                        <?php if (isset($errors['email_already_used'])): ?>
                            <li><?= $errors['email_already_used']; ?></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-light">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input
                        type="text"
                        name="full_name"
                        id="full_name"
                        class="form-control bg-dark border-secondary text-light"
                        value="<?= htmlspecialchars($full_name); ?>"
                        placeholder="Full name">
                </div>

                <?php if (isset($errors['full_name'])): ?>
                    <ul class="text-danger small mt-1 mb-0 ps-4">
                        <li><?= $errors['full_name']; ?></li>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-light">
                        <i class="fa-regular fa-face-grin"></i>
                    </span>
                    <input type="text"
                        name="pseudo"
                        id="pseudo"
                        class="form-control bg-dark border-secondary text-light"
                        value="<?= htmlspecialchars($pseudo); ?>"
                        placeholder="Pseudo">
                </div>

                <?php if (isset($errors['pseudo_length']) || isset($errors['pseudo_already_used'])): ?>
                    <ul class="text-danger small mt-1 mb-0 ps-4">
                        <?php if (isset($errors['pseudo_length'])): ?>
                            <li><?= $errors['pseudo_length']; ?></li>
                        <?php endif; ?>
                        <?php if (isset($errors['pseudo_already_used'])): ?>
                            <li><?= $errors['pseudo_already_used']; ?></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-light">
                        <i class="fa-solid fa-key"></i>
                    </span>
                    <input type="password"
                        name="password"
                        id="password"
                        class="form-control bg-dark border-secondary text-light"
                        value="<?= htmlspecialchars($password); ?>"
                        placeholder="******">
                </div>

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
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-light">
                        <i class="fa-solid fa-key"></i>
                    </span>
                    <input type="password"
                        name="password_confirm"
                        id="password_confirm"
                        class="form-control bg-dark border-secondary text-light"
                        value="<?= htmlspecialchars($password_confirm); ?>"
                        placeholder="******">
                </div>

                <?php if (isset($errors['same_password'])): ?>
                    <ul class="text-danger small mt-1 mb-0 ps-4">
                        <li><?= $errors['same_password']; ?></li>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="d-grid gap-3">
                <input type="submit" name="btnSignUp" class="btn btn-primary" value="Sign Up" id="btnSignup" disabled="disabled">
                <a href="<?= GoBack::go_back() ?>" class="btn btn-danger w-100 text-decoration-none" role="button">
                    Cancel
                </a>
            </div>
        </form>
    </main>
</body>

</html>