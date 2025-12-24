<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>My Items</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <main class="container">
        <!-- HEADER -->
        <header class="content-box">
            <nav class="navbar fixed-top navbar-dark bg-dark border-bottom shadow-lg">
                <div class="container-fluid p-2">
                    <div class="flex-grow-1 text-start" style="max-width: 5%;">
                        <a href="user/profile" class="btn-icon">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                    </div>

                    <div class="mx-auto">
                        <div class="fw-bold fs-2 text-primary">
                            My Items <i class="fa fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

    </main>
    <!-- FOOTER -->
    <footer class="content-box">
        <nav class="navbar fixed-bottom navbar-dark bg-dark border-top shadow-lg">
            <div class="container-fluid">

                <div class="d-flex justify-content-around w-100 py-3">

                    <a class="nav-link text-center text-secondary" href="main/index">
                        <i class="fa-solid fa-magnifying-glass-plus fs-4 mb-1 d-block"></i>
                        <span>Browse</span>
                    </a>

                    <a class="nav-link text-center text-secondary" href="member/items">
                        <i class="fas fa-home fs-4 mb-1 d-block"></i>
                        <span>My Items</span>
                    </a>

                    <a class="nav-link text-center text-secondary" href="member/add_offer">
                        <i class="fa-solid fa-plus fs-4 mb-1 d-block"></i>
                        <span>Add Offer</span>
                    </a>

                    <a class="nav-link text-center text-secondary" href="user/profile">
                        <i class="fa-solid fa-gear fs-4 mb-1 d-block"></i>
                        <span>Profile</span>
                    </a>
                </div>
            </div>
        </nav>
    </footer>
</body>

</html>