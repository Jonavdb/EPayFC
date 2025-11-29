<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Sign In</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- <link href="css/styles.css" rel="stylesheet" type="text/css" /> -->
    <link rel="stylesheet" href="css/styles.css?v=<?php echo filemtime('css/styles.css'); ?>" rel="stylesheet" type="text/css" />
</head>

<body>
    <main>
        <form class="form">
            <h2 class="text-center text-white">Sign in</h2>

            <div class="input-group mb-3">
                <span class="input-group-text bg-dark border-secondary text-light">
                    Email
                </span>
                <input type="email" class="form-control bg-dark border-secondary text-light" placeholder="Mail" aria-label="Mail">
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text bg-dark border-secondary text-light">
                    Password
                </span>
                <input type="password" class="form-control bg-dark border-secondary text-light" placeholder="******" aria-label="Password">
            </div>

            <div class="d-grid gap-4">
                <button type="submit" class="btn btn-primary">Login</button>
                <button type="button" class="btn btn-secondary">Continue as guest</button>
            </div>

            <!-- TODO : diriger vers sign up view quand elle sera faite -->
            <div class="text-center mt-3">
                <span class="small">New here ? <a href="#" class="text-info">Click here to subscribe !</a></span>
            </div>

            <span class="divider"></span>

            <div class="debug-section text-center">
                <h3 class="text-warning mt-3 text-bold">For Debug Purpose</h3>

                <!-- TODO : boucler depuis la DB et en faire des liens connectables -->
                <div class="debug-emails small text-light">
                    <div>Login as <strong>boverhaegen@epfc.eu</strong></div>
                    <div>Login as <strong>mamichel@epfc.eu</strong></div>
                    <div>Login as <strong>quhouben@epfc.eu</strong></div>
                    <div>Login as <strong>xapigeolet@epfc.eu</strong></div>
                </div>

                <div class="debug-actions small fw-bold">
                    <div class="mb-1 mt-1"><a href="#" class="text-success">Restore original data</a></div>
                    <div class="mb-1"><a href="#" class="text-warning">Backup personal data</a></div>
                    <div><a href="#" class="text-warning">Restore personal data</a></div>
                </div>
            </div>
        </form>
    </main>
</body>

</html>