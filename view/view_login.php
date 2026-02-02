<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Sign In</title>
    <base href="<?= $web_root ?>" />
    <meta name="author" content="HABOULA_Ali">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <main>
        <div class="mb-5">
            <h1 class="display-4"><i class="fa fa-shopping-cart display-4"></i> EPayFC</h1>
        </div>

        <div class="mt-3">
            <form id="loginForm" action="user/login" method="post">
                <h2 class="text-center text-white">Sign in</h2>
                <span class="divider"></span>

                <div class="input-group mb-3">
                    <span class="input-group-text bg-dark border-secondary text-light">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="email"
                        name="email"
                        class="form-control bg-dark border-secondary text-light"
                        placeholder="Mail"
                        value="<?= htmlspecialchars($email) ?>" aria-label="Mail">
                </div>

                <div class="input-group mb-3">
                    <span class="input-group-text bg-dark border-secondary text-light">
                        <i class="fa-solid fa-key"></i>
                    </span>
                    <input type="password"
                        name="password"
                        class="form-control bg-dark border-secondary text-light"
                        placeholder="********"
                        value="<?= htmlspecialchars($password) ?>"
                        aria-label="Password">
                </div>

                <div class="d-grid gap-3">
                    <input type="submit" class="btn btn-primary fw-normal" value="Login">
                    <a href="user/guest_login" class="btn btn-secondary fw-normal text-decoration-none">Continue as guest</a>
                </div>

                <div class="text-center mt-3">
                    <span class="small">New here ? <a href="main/signup" class="text-info">Click here to subscribe !</a></span>
                </div>

                <span class="divider"></span>

                <div class="debug-section text-center">
                    <h3 class="text-warning mt-3 text-bold">For Debug Purpose</h3>

                    <div class="debug-emails small">
                        <?php foreach ($users ?? [] as $user): ?>
                            <div class="text-light">
                                <a href="user/login?email=<?= urldecode($user->email); ?>" class="font-weight-bold">
                                    Login as <?= $user->email ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="debug-actions small fw-bold">
                        <div class="mb-1 mt-1">
                            <a href="setup/index" class="text-success">Restore original data</a>
                        </div>

                        <div class="mb-1">
                            <a href="setup/export" class="text-warning">Backup personal data</a>
                        </div>

                        <div>
                            <a href="setup/restore" class="text-warning">Restore personal data</a>
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
        </div>
    </main>
</body>

</html>