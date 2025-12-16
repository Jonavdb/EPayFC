<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>" />

    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
    <meta name="author" content="Jonathan_Vanderbiest">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Item Open</title>
    <style>
        body {
            display: block;
            padding-top: 10px;
            padding-bottom: 80px;
            height: auto;
            justify-content: unset;
            align-items: unset;
        }

        #nav-icons {
            gap: 0;
            width: 100%;
            justify-content: space-around;
            flex-direction: row;
        }
    </style>
</head>

<body>
    <nav class="navbar fixed-top navbar-dark bg-dark">
        <div class="container-fluid d-flex align-items-center">
            <div class="left-zone">
                <a href="index.php?controller=ViewOpenItem&action=show_details&param1=<?= $item->id ?>" class="text-decoration-none text-secondary">
                    <i class="fa-solid fa-arrow-left" style="color: #2D68C4; font-size: 22px;"></i>
                </a>
            </div>
            <div class="flex-grow-1 text-center">
                <h2 style="color: #2D68C4; margin: 0;">Manage Images</h2>
            </div>
        </div>
    </nav>
    <div class="container-fluid mb-4 px-5" style="margin-top: 80px;">
        <h4 class="text-white text-start">
            Manage Images for "<?= $item->title ?>"
        </h4>
    </div>
    <div class="container-fluid mb-4 px-5">
        <div class="card text-bg-dark border-secondary">
            <div class="card-header border-secondary" style="background-color: #1e293b;">
                <h5 class="mb-0 text-white text-start">Add new Images</h5>
            </div>
            <div class="card-body">
                <h6 class="fw-bold text-white text-start">Select Images</h6>
                <form action="index.php?controller=ViewOpenItem&action=add_images" method="POST" enctype="multipart/form-data">

                    <input type="hidden" name="item_id" value="<?= $item->id ?>">

                    <input class="form-control bg-dark text-secondary border-secondary"
                        type="file"
                        name="image_files[]"
                        multiple>
                    <div class="small text-secondary mt-2">
                        You can select multiple images (JPG, PNG, GIF, WEBP).Images will be added to the end of the current list.
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            Upload Images
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="container-fluid mb-4 px-5">
        <div class="card text-bg-dark border-secondary">
            <div class="card-header border-secondary" style="background-color: #1e293b;">
                <h5 class="mb-0 text-white text-start">Current Images</h5>
            </div>
            <div class="card-body">
                <?php if (empty($item->image_urls)) { ?>
                    <p class="text-secondary m-0">No images uploaded yet.</p>
                <?php } else { ?>

                <?php } ?>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-dark bg-dark">
        <ul class="navbar-nav bottom-nav" id="nav-icons">
            <li class="nav-item text-center">
                <a href="index.php?controller=BrowseItems&action=show_items" class="nav-link"><i class="fa-solid fa-magnifying-glass d-block"></i><small style="font-size: 10px;">Browse</small></a>
            </li>
            <li class="nav-item text-center">
                <a href="index.php?controller=ViewOpenItem&action=show_my_items" class="nav-link"><i class="fa-solid fa-house d-block"></i><small style="font-size: 10px;">My Items</small></a>
            </li>
            <li class="nav-item text-center">
                <a href="index.php?controller=ViewOpenItem&action=add_offer" class="nav-link"><i class="fa-solid fa-plus d-block"></i><small style="font-size: 10px;">Add Offer</small></a>
            </li>
            <li class="nav-item text-center">
                <a href="index.php?controller=User&action=see_profile" class="nav-link"><i class="fa-solid fa-gear d-block"></i><small style="font-size: 10px;">Profile</small></a>
            </li>
        </ul>
    </nav>

    <div class="d-flex justify-content-between align-items-center px-3" style="
        background-color: #1e293b; 
        border-top: 1px solid #334155; 
        color: #94a3b8;
        height: 40px;
        font-size: 11px;
    ">
        <span class="fw-bold text-white">
            <?php
            $dt = new DateTime(AppTime::get_current_datetime());
            echo $dt->format('d/m/y H:i');
            ?>
        </span>

        <div class="d-flex align-items-center">

            <form method="post" action="time/advance" class="m-0">
                <input type="hidden" name="amount" value="1"><input type="hidden" name="unit" value="hour">
                <button class="btn btn-debug text-debug-orange">
                    <i class="fa-regular fa-clock me-1"></i>+1h
                </button>
            </form>

            <form method="post" action="time/advance" class="m-0">
                <input type="hidden" name="amount" value="1"><input type="hidden" name="unit" value="day">
                <button class="btn btn-debug text-debug-orange">
                    <i class="fa-regular fa-calendar me-1"></i>+1d
                </button>
            </form>

            <form method="post" action="time/advance" class="m-0">
                <input type="hidden" name="amount" value="7"><input type="hidden" name="unit" value="day">
                <button class="btn btn-debug text-debug-orange">
                    <i class="fa-regular fa-calendar-days me-1"></i>+1w
                </button>
            </form>

            <form method="post" action="time/reset" class="m-0">
                <button class="btn btn-debug text-debug-red">
                    <i class="fa-solid fa-rotate-right me-1"></i>
                </button>
            </form>
        </div>
    </div>

    </div>
</body>

</html>