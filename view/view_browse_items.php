<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Browse Items</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" type="text/css" href="css/item.css">
    <link rel="stylesheet" type="text/css" href="css/browse_items.css">

    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- HEADER -->
    <header class="content-box">
        <nav class="navbar fixed-top navbar-dark bg-dark border-top shadow-lg">
            <div class="container-fluid d-flex align-items-center">
                <div class="flex-grow-1 text-center">
                    <div class="fw-bold fs-2" style="color: #2D68C4;"> Browse <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- PAGE CONTENT -->
    <main class="flex-grow-1 content-box">

        <!-- CONTAINER 1 direction vertcial -->
        <div class="d-flex flex-column min-vh-100">

            <!-- Text : "Items i'm participating in" -->
            <div class="fw-bold fs-2" style="color: #2D68C4;">Items I'm participating in</div>

            <!-- Vertical container that takes remaining space automatically -->
            <div class="p-3 d-flex flex-column justify-content-evenly h-100">

                <!-- Item cards wrapping container -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 ">
                    <!-- 1 item / col -->
                    <div class="col">
                        <div class="card dark-theme w-100 h-100">
                            <!-- TOP CELL -->
                            <div class="row">
                                <div class="col-sm">
                                    <img class="card-img-top" src="uploads/items/1/25cdaf4224c6841a_1762786661_thumbnail.jpg" alt="Auction Item Image">

                                    <!-- Everything that is on top of the item image -->
                                    <div class="card-img-overlay" style="height:55%;">

                                        <!-- Direction : Vertical -->
                                        <div class="d-flex flex-column h-100">

                                            <!-- Row 1 : Top left & Top right -->
                                            <div class="d-flex justify-content-between">

                                                <!-- Top left -->
                                                <div>
                                                    <span class="badge rounded-pill bg-dark badge-bidder">
                                                        <i class="fa-solid fa-gavel flipped-horizontal"></i> Bidder
                                                    </span>
                                                </div>

                                                <!-- Top right -->
                                                <div class="d-flex flex-column">
                                                    <span class="badge rounded-pill bg-warning text-dark badge-auction mb-1">
                                                        <i class="fa-solid fa-gavel flipped-horizontal"></i> Auction
                                                    </span>
                                                    <span class="badge rounded-pill bg-primary">
                                                        <i class="fa-solid fa-bag-shopping"></i> Buy now
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Spacer between Row 1 and Row 2 -->
                                            <div class="flex-grow-1"></div>

                                            <!-- Row 2 : Bottom right -->
                                            <div class="d-flex justify-content-end">
                                                <span class="badge rounded-pill bg-dark">
                                                    <i class="fa-solid fa-images"></i> 2 images
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BOTTOM CELL -->
                            <div class="row">
                                <div class="col-sm">
                                    <div class="card-body">

                                        <!-- Direction : Vertical -->
                                        <div class="d-flex flex-column">

                                            <!-- Row 1 : Top left (using bootstrap class "card title") -->
                                            <div class="card-title">
                                                <b>Iphone 17</b>
                                                <div>by Apple</div>
                                            </div>

                                            <!-- Row 2 : Middle left & Middle right (using bootstrap class "card text") -->
                                            <div class="card-text d-flex justify-content-between price-row mt-2 mb-2">

                                                <!-- Mid left -->
                                                <div class="price-left">€ 286,00</div>

                                                <!-- Mid right -->
                                                <div class="d-flex flex-column text-end bid-info">
                                                    <div>Current bid</div>
                                                    <div class="bid-price">€ 162,00</div>
                                                </div>
                                            </div>

                                            <!-- Row 3 : Bottom left (below boostrap class "card text"-->
                                            <div class="time-left">
                                                <i class="fa-solid fa-clock transparent-icon"></i> 1d 4h left
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- CONTAINER 2 direction vertical "other available items" -->
        <div class="d-flex flex-column min-vh-100">

            <!-- Text : "Items i'm participating in" -->
            <div class="fw-bold fs-2" style="color: #2D68C4;">Other Available Items</div>

            <!-- Vertical container that takes remaining space automatically -->
            <div class="p-3 d-flex flex-column justify-content-evenly h-100">

                <!-- Item cards wrapping container -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 ">
                    <!-- 1 item / col -->
                    <div class="col">
                        <div class="card dark-theme w-100 h-100">
                            <!-- TOP CELL -->
                            <div class="row">
                                <div class="col-sm">
                                    <img class="card-img-top" src="uploads/items/1/25cdaf4224c6841a_1762786661_thumbnail.jpg" alt="Auction Item Image">

                                    <!-- Everything that is on top of the item image -->
                                    <div class="card-img-overlay" style="height:55%;">

                                        <!-- Direction : Vertical -->
                                        <div class="d-flex flex-column h-100">

                                            <!-- Row 1 : Top left & Top right -->
                                            <div class="d-flex justify-content-between">

                                                <!-- Top left -->
                                                <div>
                                                    <span class="badge rounded-pill bg-dark badge-bidder">
                                                        <i class="fa-solid fa-gavel flipped-horizontal"></i> Bidder
                                                    </span>
                                                </div>

                                                <!-- Top right -->
                                                <div class="d-flex flex-column">
                                                    <span class="badge rounded-pill bg-warning text-dark badge-auction mb-1">
                                                        <i class="fa-solid fa-gavel flipped-horizontal"></i> Auction
                                                    </span>
                                                    <span class="badge rounded-pill bg-primary">
                                                        <i class="fa-solid fa-bag-shopping"></i> Buy now
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Spacer between Row 1 and Row 2 -->
                                            <div class="flex-grow-1"></div>

                                            <!-- Row 2 : Bottom right -->
                                            <div class="d-flex justify-content-end">
                                                <span class="badge rounded-pill bg-dark">
                                                    <i class="fa-solid fa-images"></i> 2 images
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BOTTOM CELL -->
                            <div class="row">
                                <div class="col-sm">
                                    <div class="card-body">

                                        <!-- Direction : Vertical -->
                                        <div class="d-flex flex-column">

                                            <!-- Row 1 : Top left (using bootstrap class "card title") -->
                                            <div class="card-title">
                                                <b>Micro Blue Yeti Black</b>
                                                <div>by Boris</div>
                                            </div>

                                            <!-- Row 2 : Middle left & Middle right (using bootstrap class "card text") -->
                                            <div class="card-text d-flex justify-content-between price-row mt-2 mb-2">

                                                <!-- Mid left -->
                                                <div class="price-left">€ 286,00</div>

                                                <!-- Mid right -->
                                                <div class="d-flex flex-column text-end bid-info">
                                                    <div>Current bid</div>
                                                    <div class="bid-price">€ 162,00</div>
                                                </div>
                                            </div>

                                            <!-- Row 3 : Bottom left (below boostrap class "card text"-->
                                            <div class="time-left">
                                                <i class="fa-solid fa-clock transparent-icon"></i> 1d 4h left
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
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

</html>