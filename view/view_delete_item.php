<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>" />
    <title>Delete Item</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body class="bg-body-tertiary d-flex align-items-center justify-content-center min-vh-100">

    <main class="container" style="max-width: 450px;">
        <div class="card border-danger shadow-lg">
            <div class="card-body text-center p-5">

                <div class="mb-4">
                    <i class="fa-solid fa-trash-can fa-4x text-danger"></i>
                </div>

                <h1 class="fw-bold mb-5 text-danger">Are you sure?</h1>
                <span class="divider mb-3"></span>

                <p class="text-light mb-4">
                    Do you really want to delete item <span class="fw-bold">"<?= htmlspecialchars($item->title) ?>"</span>
                    by <?= htmlspecialchars($user->full_name) ?> and all of its dependencies?
                    <br>
                    <br>
                    This process cannot be undone.
                </p>

                <form action="item/delete/<?= $item->id ?>" method="post">
                    <a href="item/open/<?= $item->id ?>" class="btn btn-secondary px-4">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-danger px-4">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </main>

</body>

</html>