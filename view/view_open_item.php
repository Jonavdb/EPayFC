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
        <i class="fa-solid fa-arrow-left" style="color: #2D68C4; font-size: 22px;"></i>
      </div>
      <div class="flex-grow-1 text-center">
        <h2 style="color: #2D68C4; margin: 0;">Open item</h2>
      </div>
    </div>
  </nav>

  <div class="container-fluid px-4 pb-4 pt-0" style="margin-top: 40px; margin-bottom: 5px;">
    <div class="row">
      <div class="col-md-8">
        <div class="card text-bg-dark mb-3">

          <img src="test.jpg" class="card-img-top" alt="Piano" style="max-height: 500px; object-fit: cover;">

          <div class="card-body">
            <p class="card-title"><?= $item->title ?></p>
            <p class="card-text text-secondary"><?= $item->description ?></p>

            <div class="small">
              <p class="mb-2">starts at : <?= $item->created_at->format('d/m/Y H:i') ?></p>
              <p>ends at : <?= $item->created_at->format('d/m/Y H:i') ?> </p>
            </div>
          </div>
        </div>
        <h4 class="text-white mt-4 mb-4">Additionals Images</h4>
        <div class="d-flex gap-4 mb-4">
          <?php
          foreach ($item->image_urls as $image):
          ?>
            <img src="<?= $image ?>"
              class="additionnal-images"
              alt="Vue supplémentaire">
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-bg-dark mb-3 border-light">
          <div class="card-body">
            <h5>Pricing</h5>
            <div class="d-flex justify-content-between mb-2">
              <span>Current Bid</span>
              <h3 class="text-success">€ <?= $item->current_bid ?></h3>
            </div>
            <div class="d-flex justify-content-between mb-3">
              <span>Buy Now</span>
              <h3 class="text-success">€ <?= $item->price ?></h3>
            </div>
            <div class="d-grid gap-3">
              <button class="btn btn-success" type="button">Place Bid</button>
              <button class="btn btn-outline-primary" type="button">Buy Now</button>
            </div>
          </div>
        </div>

        <div class="card text-bg-dark mb-3 border-light">
          <div class="card-body">
            <h5>Seller</h5>
            <div class="d-flex align-items-center mt-2">
              <i class="fa-solid fa-circle-user fa-2x me-2 text-secondary"></i>
              <div>
                <div class="fw-bold"><?= $item->owner_name ?></div>
                <div class="small text-secondary"><?= $item->owner_status ?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid px-4 pb-4 pt-0" style="margin-top: 0px; margin-bottom: 5px;">
    <div class="row">
      <div class="col-md-8">
        <div class="card text-bg-dark mb-3">
          <div class="card-header border-secondary d-flex justify-content-between align-items-center">
            <span>Bid History</span>
            <span class="badge bg-light text-dark"><?= count($bids) ?> entries</span>
          </div>
          <ul class="list-group list-group-flush">
            <?php foreach ($bids as $bid): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-bold"><?= $bid['user'] ?></div>
                  <small class="text-secondary"><?= $bid['time'] ?></small>
                </div>

                <span class="<?= $bid['active'] ? 'text-success fw-bold' : 'text-success' ?>">
                  € <?= $bid['amount'] ?>
                </span>

              </li>
            <?php endforeach ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="fixed-bottom">

    <nav class="navbar navbar-dark bg-dark">
      <ul class="navbar-nav bottom-nav" id="nav-icons">
        <li class="nav-item text-center">
          <a href="#" class="nav-link"><i class="fa-solid fa-magnifying-glass d-block"></i><small style="font-size: 10px;">Browse</small></a>
        </li>
        <li class="nav-item text-center">
          <a href="#" class="nav-link"><i class="fa-solid fa-house d-block"></i><small style="font-size: 10px;">My Items</small></a>
        </li>
        <li class="nav-item text-center">
          <a href="#" class="nav-link"><i class="fa-solid fa-plus d-block"></i><small style="font-size: 10px;">Add Offer</small></a>
        </li>
        <li class="nav-item text-center">
          <a href="#" class="nav-link"><i class="fa-solid fa-gear d-block"></i><small style="font-size: 10px;">Profile</small></a>
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