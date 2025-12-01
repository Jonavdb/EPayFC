<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Sign up</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <main>
        <form class="form">
            <h2 class="text-center text-white">Sign up</h2>
            <span class="divider"></span>

            <!-- EMAIL -->
            <div class="input-group mb-3">
                <span class="input-group-text bg-dark border-secondary text-light">
                    <i class="fa-solid fa-envelope"></i>
                </span>
                <input type="email" id="email" class="form-control bg-dark border-secondary text-light" placeholder="Mail" aria-label="Mail">
            </div>

            <!-- FULL NAME -->
            <div class="input-group mb-3">
                <span class="input-group-text bg-dark border-secondary text-light">
                    <i class="fa-solid fa-user"></i>
                </span>
                <input type="text" id="full_name" class="form-control bg-dark border-secondary text-light" placeholder="Full name" aria-label="Full Name">
            </div>

            <!-- PSEUDO -->
            <div class="input-group mb-3">
                <span class="input-group-text bg-dark border-secondary text-light">
                    <i class="fa-regular fa-face-grin"></i>
                </span>
                <input type="text" id="pseudo" class="form-control bg-dark border-secondary text-light" placeholder="Pseudo" aria-label="Pseudo">
            </div>

            <!-- PASSWORD -->
            <div class="input-group mb-3">
                <span class="input-group-text bg-dark border-secondary text-light">
                    <i class="fa-solid fa-key"></i>
                </span>
                <input type="password" id="password" class="form-control bg-dark border-secondary text-light" placeholder="******" aria-label="Password">
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="input-group mb-3">
                <span class="input-group-text bg-dark border-secondary text-light">
                    <i class="fa-solid fa-key"></i>
                </span>
                <input type="password" id="confirm_password" class="form-control bg-dark border-secondary text-light" placeholder="******" aria-label="Confirm_password">
            </div>

            <!-- Boutons -->
            <div class="d-grid gap-3">
                <button type="submit" class="btn btn-primary">Sign Up</button>
                <button type="button" class="btn btn-danger">Cancel</button>
            </div>
        </form>
    </main>
</body>

</html>