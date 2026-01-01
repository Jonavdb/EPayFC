<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delete Item</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body class="bg-body-tertiary d-flex align-items-center justify-content-center min-vh-100">

    <main class="container" style="max-width: 500px;">
        <div class="card border-danger shadow-lg">
            <div class="card-body text-center p-5">

                <div class="mb-4">
                    <i class="fa-solid fa-trash-can fa-4x text-danger"></i>
                </div>

                <h3 class="fw-bold mb-3 text-danger">Are you sure?</h3>

                <p class="text-secondary mb-4">
                    Do you really want to delete item <strong class="text-white">"<?= htmlspecialchars($item->title) ?>"</strong>
                    by <?= htmlspecialchars($user->full_name) ?> and all of its dependencies?
                    <br><br>
                    <span class="text-danger fw-bold">This process cannot be undone.</span>
                </p>

                <form action="item/delete/<?= $item->id ?>" method="post" class="d-flex gap-3 justify-content-center">

                    <a href="item/open/<?= $item->id ?>/from-items" class="btn btn-secondary px-4">
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