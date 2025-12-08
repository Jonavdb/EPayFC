<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Sign In</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <main>
        <form id="loginForm" action="user/login" method="post">
            <h2 class="text-center text-white">Sign in</h2>
            <span class="divider"></span>

            <div class="input-group mb-3">
                <span class="input-group-text bg-dark border-secondary text-light">
                    <i class="fa-solid fa-user"></i>
                </span>
                <input type="email" name="email" class="form-control bg-dark border-secondary text-light" placeholder="Mail" aria-label="Mail">
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text bg-dark border-secondary text-light">
                    <i class="fa-solid fa-key"></i>
                </span>
                <input type="password" name="password" class="form-control bg-dark border-secondary text-light" placeholder="********" aria-label="Password">
            </div>

            <div class="d-grid gap-3">
                <input type="submit" class="btn btn-primary fw-normal" value="Login">
                <button type="button" class="btn btn-secondary fw-normal">Continue as guest</button>
            </div>

            <div class="text-center mt-3">
                <span class="small">New here ? <a href="main/signup" class="text-info">Click here to subscribe !</a></span>
            </div>

            <span class="divider"></span>

            <div class="debug-section text-center">
                <h3 class="text-warning mt-3 text-bold">For Debug Purpose</h3>

                <div class="debug-emails small">
                    <div class="text-light">
                        <a href="user/login?email=boverhaegen@epfc.eu" class="font-weight-bold">Login as boverhaegen@epfc.eu</a>
                    </div>

                    <div>
                        <a href="user/login?email=mamichel@epfc.eu" class="font-weight-bold">Login as mamichel@epfc.eu</a>
                    </div>

                    <div>
                        <a href="user/login?email=quhouben@epfc.eu" class="font-weight-bold">Login as quhouben@epfc.eu</a>
                    </div>

                    <div>
                        <a href="user/login?email=xapigeolet@epfc.eu" class="font-weight-bold">Login as xapigeolet@epfc.eu</a>
                    </div>
                </div>

                <!-- TODO : attribuer ce qu'il faut -->
                <div class="debug-actions small fw-bold">
                    <div class="mb-1 mt-1"><a href="#" class="text-success">Restore original data</a></div>
                    <div class="mb-1"><a href="#" class="text-warning">Backup personal data</a></div>
                    <div><a href="#" class="text-warning">Restore personal data</a></div>
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
</body>

</html>