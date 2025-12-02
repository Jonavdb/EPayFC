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
            <p class="card-title"><?= $item['title'] ?></p>
            <p class="card-text text-secondary"><?= $item['description'] ?></p>

            <div class="small">
              <p class="mb-0">starts at : <?= $item['start_date'] ?></p>
              <p>ends at : <?= $item['end_date'] ?> </p>
            </div>
          </div>

        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-bg-dark mb-3 border-light">
          <div class="card-body">
            <h5>Pricing</h5>
            <div class="d-flex justify-content-between mb-2">
              <span>Current Bid</span>
              <h3 class="text-success">€ <?= $item['current_bid'] ?></h3>
            </div>
            <div class="d-flex justify-content-between mb-3">
              <span>Buy Now</span>
              <h3 class="text-success">€ <?= $item['buy_now_price'] ?></h3>
            </div>
            <div class="d-grid gap-2">
              <button class="btn btn-success" type="button">Place Bid</button>
              <button class="btn btn-outline-primary" type="button">Buy Now</button>
            </div>
          </div>
        </div>

        <div class="card text-bg-dark mb-3 border-light">
          <div class="card-body">
            <h5>Seller</h5>
            <div class="d-flex align-items-center mt-3">
              <i class="fa-solid fa-circle-user fa-2x me-2 text-secondary"></i>
              <span><?= $item['seller'] ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <p>Additionnal Images</p>

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
  <nav class="navbar fixed-bottom navbar-expand-lg navbar-dark bg-dark">
    <ul class="navbar-nav mx-auto" id="nav-icons">
      <li class="nav-item">
        <div class="icon-box text-center text-white px-3">
          <i class="fa-solid fa-magnifying-glass"></i><br>
          <span style="font-size: 12px;">Browse</span>
        </div>
      </li>
      <li class="nav-item">
        <div class="icon-box text-center text-white px-3">
          <i class="fa-solid fa-house"></i><br>
          <span style="font-size: 12px;">My Items</span>
        </div>
      </li>
      <li class="nav-item">
        <div class="icon-box text-center text-white px-3">
          <i class="fa-solid fa-plus"></i><br>
          <span style="font-size: 12px;">Add Offer</span>
        </div>
      </li>
      <li class="nav-item">
        <div class="icon-box text-center text-white px-3">
          <i class="fa-solid fa-gear"></i><br>
          <span style="font-size: 12px;">Profile</span>
        </div>
      </li>
    </ul>
  </nav>

</body>

</html>