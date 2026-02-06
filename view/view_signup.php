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
</head>

<body>
    <main>
        <form id="signupForm" action="main/signup" method="post">
            <h2 class="text-center text-white">Sign up</h2>
            <span class="divider"></span>

            <!-- EMAIL -->
            <div class="input-group mb-3">
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

            <!-- FULL NAME -->
            <div class="input-group mb-3">
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

            <!-- PSEUDO -->
            <div class="input-group mb-3">
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

            <!-- PASSWORD -->
            <div class="input-group mb-3">
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

            <!-- CONFIRM PASSWORD -->
            <div class="input-group mb-3">
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

            <!-- Boutons -->
            <div class="d-grid gap-3">
                <input type="submit" class="btn btn-primary" value="Sign Up">
                <a href="<?= GoBack::go_back() ?>" class="btn btn-danger w-100" role="button">
                    Cancel
                </a>
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
</body>

</html>