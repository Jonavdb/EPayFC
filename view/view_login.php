<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Sign In</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <main class="form-container">
        <h1>🛒 EPAyFC</h1>
        <br>

        <form>
            <h2 class="text-center">Sign In</h2>
            <span class="divider"></span>

            <div>
                <label for="email">Mail</label>
                <input type="email" id="email" placeholder="Enter your email">
            </div>

            <div>
                <label for="password">Login</label>
                <input type="password" id="password" placeholder="Enter your password">
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
            <button type="submit" class="btn btn-secondary">Continue as guest</button>

            <div class="text-center mt-3">
                <p class="text-muted">New here? <a href="#">Click here to subscribe!</a></p>
            </div>
        </form>
    </main>
</body>

</html>