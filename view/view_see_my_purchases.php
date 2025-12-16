<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Purchases</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" type="text/css" href="css/see_my_purchases.css">

    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- HEADER -->
    <header class="content-box">
        <nav class="navbar fixed-top navbar-dark bg-dark border-top shadow-lg">

            <!--  takes up 5% of row width for centering -->
            <!-- left : back button -->
            <div class="flex-grow-1 text-start" style="max-width: 5%;">
                <button class="btn btn-link d-inline-flex justify-content-start" style="padding-left: 0.5rem;">
                    <i class="fa-solid fa-arrow-left text-primary"></i>
                </button>
            </div>

            <!-- mx-auto is margin-left: auto; margin-right: auto; for centering -->
            <!-- middle : title  -->
            <div class="mx-auto">
                <div class="fw-bold fs-2" style="color: #2D68C4;">
                    Purchases <i class="fa-solid fa-cart-shopping"></i>
                </div>
            </div>

            <!--  takes up 5% of row width for centering -->
            <!-- right : placeholder to make page title perfectly centered  -->
            <div class="flex-grow-1 text-end invisible" style="max-width: 5%;">
                <button class="btn btn-primary"></button>
            </div>
        </nav>
    </header>

    <!-- PAGE CONTENT -->
    <main class="flex-grow-1 content-box">

        <!-- Top level container that aligns sub-element at the start vertically -->
        <div class="p-3 d-flex flex-column justify-content-start h-100">

            <!-- Top container with global infos -->
            <div class="p-3 d-flex flex-column h-100 gap-3">

                <!-- .. -->
                <div class="d-flex flex-row w-100 align-items-center">
                    <!--  takes up 5% of row width for centering -->
                    <!-- left : vertical container with 2 texts -->
                    <div class="d-flex flex-grow-1" style="max-width: 15%;">
                        <div class="d-flex flex-column lh-sm">
                            <span class="fw-bold small">My Purchases</span>
                            <span class="fw-bold small">Track the gear you've succesfully secured.</span>
                        </div>
                    </div>

                    <!-- mx-auto is margin-left: auto; margin-right: auto; for centering -->
                    <!-- middle : gap -->
                    <div class="mx-auto text-center">
                        <div class="fw-bold fs-4"></div>
                    </div>


                    <!--  takes up 5% of row width for centering -->
                    <!-- right : placeholder to make page title perfectly centered  -->
                    <div class="flex-grow-1 text-end" style="max-width: 15%;">
                        <span class="badge rounded-pill bg-primary">New</span>
                    </div>
                </div>

                <!-- .. -->
                <div class="flex-grow-1 ">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                        <div class="col">
                            <div class="p-3 border bg-dark h-100">Content</div>
                        </div>
                        <div class="col">
                            <div class="p-3 border bg-dark h-100">Content</div>
                        </div>
                        <div class="col">
                            <div class="p-3 border bg-dark h-100">Content</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="content-box">
        <nav class="navbar fixed-bottom navbar-dark bg-dark">
            <!-- Use the 'container-fluid' or 'w-100' for full width in the footer -->
            <div class="container-fluid">

                <div class="d-flex justify-content-around w-100">
                    <!-- Browse Link -->
                    <a class="bottom-nav-link text-decoration-none" href="#">
                        <div class="d-flex flex-column">
                            <div class="d-flex justify-content-center w-100"><i class="fa-solid fa-magnifying-glass-plus"></i></div>
                            <div>Browse</div>
                        </div>
                    </a>


                    <!-- My Items Link -->
                    <a class="bottom-nav-link text-decoration-none" href="#">
                        <div class="d-flex flex-column">
                            <div class="d-flex justify-content-center w-100"><i class="fas fa-home"></i></div>
                            <div>My Items</div>
                        </div>
                    </a>

                    <!-- Add Offer Link -->
                    <a class="bottom-nav-link text-decoration-none" href="#">
                        <div class="d-flex flex-column">
                            <div class="d-flex justify-content-center w-100"><i class="fa-solid fa-plus"></i></div>
                            <div>Add Offer</div>
                        </div>
                    </a>


                    <!-- Profile Link -->
                    <a class="bottom-nav-link text-decoration-none" href="#">
                        <div class="d-flex flex-column">
                            <div class="d-flex justify-content-center w-100"><i class="fa-solid fa-gear"></i></div>
                            <div>Profile</div>
                        </div>
                    </a>
                </div>
            </div>
        </nav>
    </footer>
</body>