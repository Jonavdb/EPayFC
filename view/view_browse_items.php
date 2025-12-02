<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Browse Items</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* -------------------------------------- */
        /* --- GENERAL CARD AND THEME STYLES --- */
        /* -------------------------------------- */
        .card.dark-theme {
            background-color: #1a1a1a;
            color: #fff;
            border-radius: 0.5rem;
            border: none;
            overflow: hidden;
        }

        .card.dark-theme .card-body {
            background-color: #1a1a1a;
            padding: 1rem;
        }

        /* -------------------------------------- */
        /* --- OVERLAY (TOP) STYLES --- */
        /* -------------------------------------- */

        /* Ensure consistent badge sizing and text color */
        .card-img-overlay .badge {
            padding: 0.4em 0.7em;
            font-size: 0.8em;
            line-height: 1.2;
        }

        /* FIX: Bidder badge add padding around (a little bit) */
        .badge-bidder {
            padding: 0.5em 0.8em !important;
        }

        /* FIX: Auction text in white & space between Auction and Buy Now */
        .badge-auction {
            color: #fff !important;
            /* Force white text */
            margin-right: 0.3rem;
        }

        /* FIX: Hammer icon flip */
        .fa-gavel.flipped-horizontal {
            transform: scaleX(-1);
        }

        /* -------------------------------------- */
        /* --- CARD BODY (BOTTOM) STYLES --- */
        /* -------------------------------------- */

        /* Card Title and Author Styling */
        .card.dark-theme .card-title b {
            color: #fff;
        }

        .card.dark-theme .card-title div {
            color: #b0b0b0;
            font-size: 0.9em;
        }

        /* FIX: Price text lower the position (Vertical Alignment) */
        .price-row {
            /* Align items vertically centered instead of baseline */
            align-items: center !important;
        }

        /* Middle Left: Main Price in turquoise bold */
        .price-left {
            color: #00bcd4;
            font-weight: bold;
            font-size: 1.1em;
            /* Slight downward shift might be needed to perfectly center */
            margin-top: 0.05rem;
        }

        /* Middle Right: Current Bid info, smaller text */
        .bid-info {
            font-size: 0.85em;
            color: #b0b0b0;
        }

        .bid-price {
            color: #66bb6a;
        }

        /* Bottom Left: Time Left with transparent clock icon */
        .time-left {
            color: #b0b0b0;
            font-size: 0.9em;
        }

        .fa-clock.transparent-icon {
            opacity: 0.7;
            /* Make the clock icon slightly transparent */
        }
    </style>
</head>

<body>
    <!-- GRID LAYOUT -->
    <div class="container">
        <div class="card dark-theme" style="width: 18rem;">

            <!-- TOP CELL -->
            <div class="row">
                <div class="col-sm">
                    <img class="card-img-top" src="uploads/items/1/25cdaf4224c6841a_1762786661_thumbnail.jpg" alt="Auction Item Image">

                    <!-- Everything that is on top of the item image -->
                    <div class="card-img-overlay" style="height:54%;">

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

</body>

</html>