<?php

require_once "utils/GoBack.php";
$buy_now = $item->buy_now_price;
$now = new DateTime(AppTime::get_current_datetime());
$is_time_expired = $now > $item->end_at;

$auction_finished = $is_time_expired || $item->buy_now_reached;

$winner = null;
if (!empty($bids)) {
  $winner = $bids[0]->owner_pseudo;
}

$image_to_display = $item->main_image_url;

if (isset($_GET['selected_image']) && !empty($_GET['selected_image'])) {
  if (in_array($_GET['selected_image'], $item->image_urls)) {
    $image_to_display = $_GET['selected_image'];
  }
}
?>


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
         <a href="<?= GoBack::go_back() ?>" class="btn-icon">
                    
        <i class="fa-solid fa-arrow-left" style="color: #2D68C4; font-size: 22px;"></i>
         </a>
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

          <img src="<?= $image_to_display ?>" class="card-img-top" alt="Piano" style="max-height: 500px; object-fit: cover;">
          <div class="card-body">
            <p class="card-title"><?= $item->title ?></p>
            <p class="card-text text-secondary"><?= $item->description ?></p>

            <div class="small">
              <p class="mb-2">starts at : <?= $item->created_at->format('d/m/Y H:i') ?></p>
              <p>ends at : <?= $item->end_at->format('d/m/Y H:i') ?> </p>
            </div>
          </div>
        </div>
        <h4 class="text-white mt-4 mb-4">Additionals Images</h4>
        <div class="d-flex gap-4 mb-4">
          <?php
          foreach ($item->image_urls as $image):
          ?>
            <a href="index.php?controller=item&action=open&param1=<?= $item->id ?>&selected_image=<?= $image ?>">
              <img src="<?= $image ?>"
                class="additionnal-images"
                alt="Vue supplémentaire">
            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-bg-dark mb-3 border-light">
          <div class="card-body">
            <h5>Pricing</h5>
            <?php if (!$item->is_direct_sale) { ?>
              <div class="d-flex justify-content-between mb-2">
                <span>Current Bid</span>
                <h3 class="text-success">€ <?= $item->current_bid ?></h3>
              </div>
              <?php }

            if ($auction_finished) {

              if ($winner) {
                $current_user_pseudo = "Marc";
                if ($winner === $current_user_pseudo) {
              ?>
                  <div class="alert alert-dark border-secondary text-secondary mt-3">
                    Congratulations! You purchased this item for € <?= $item->current_bid ?>.
                  </div>

                <?php } else { ?>
                  <div class="alert alert-warning mt-3">
                    This item was purchased by <strong><?= $winner ?></strong>.
                  </div>
                <?php } ?>
              <?php } else { ?>
                <div class="alert alert-secondary mt-3">
                  <i class="fa-regular fa-clock me-2"></i>
                  This auction has ended. <strong>No bids were placed.</strong>
                </div>
              <?php } ?>

            <?php } else { ?>

              <?php if ($item->has_buy_now) { ?>
                <div class="d-flex justify-content-between mb-3">
                  <span>Buy Now</span>
                  <h3 class="text-success">€ <?= $item->buy_now_price ?></h3>
                </div>
              <?php } ?>

              <?php if (!$item->is_direct_sale) { ?>

                <form action="index.php?controller=item&action=new_bid" method="POST" class="mb-3">

                  <input type="hidden" name="item_id" value="<?= $item->id ?>">

                  <div class="d-grid gap-3">

                    <div>
                      <label class="form-label small text-muted">Your Max Bid</label>
                      <div class="input-group">
                        <span class="input-group-text">€</span>

                        <input type="number"
                          class="form-control"
                          name="amount"
                          value="<?= $item->current_bid + 1 ?>"
                          min="<?= $item->current_bid + 1 ?>"
                          step="1"
                          required
                          <?php if ($current_user === $item->owner_name) echo 'disabled'; ?>>

                        <button class="btn btn-success" type="submit" <?php if ($current_user === $item->owner_name) echo 'disabled'; ?>>Place Bid</button>
                      </div>
                    </div>
                  </div>
                </form>
              <?php } ?>
              <div class="d-grid gap-3 mb-2">
                <?php if ($item->has_buy_now) { ?>
                  <form action="index.php?controller=item&action=buy_now" method="POST">
                    <input type="hidden" name="item_id" value="<?= $item->id ?>" <?php if ($current_user === $item->owner_name) echo 'disabled'; ?>>
                    <button class="btn btn-outline-primary w-100" type="submit" <?php if ($current_user === $item->owner_name) echo 'disabled'; ?>>Buy Now</button>
                  </form>
                <?php } ?>
              </div>
            <?php } ?>
            <?php if ($current_user === $item->owner_name) { ?>
              <h5>Vous ne pouvez pas enchérir sur un objet vous appartenant</h5>
            <?php } ?>
          </div>
        </div>
        <div class="card text-bg-dark mb-3 border-light">
          <div class="card-body">
            <h5>Seller</h5>
            <div class="d-flex align-items-center mt-2">

              <img src="<?= $item->owner_picture ?>"
                alt="Avatar"
                class="rounded-circle me-2 border border-secondary"
                style="width: 40px; height: 40px; object-fit: cover;">

              <div class="d-flex flex-column justify-content-center">

                <div class="fw-bold">
                  <?= $item->owner_name ?>
                </div>

                <div class="small text-secondary">
                  <?= $item->owner_role ?>
                </div>

              </div>

            </div>
          </div>
        </div>
        <?php if ($current_user === $item->owner_name && (!$item->has_bids)) { ?>
          <div class="card text-bg-dark mb-3 border light">
            <div class="card-body">
              <h5>Manage your Item</h5>
              <div class="d-grid gap-2">
                <a href="index.php?controller=Item&action=edit&param1=<?= $item->id ?>"
                  class="btn btn-outline-secondary text-start">
                  <i class="fa-solid fa-pen me-2"></i> Edit Item Details
                </a>

                <a href="index.php?controller=Item&action=manage_images&param1=<?= $item->id ?>"
                  class="btn btn-outline-info text-start">
                  <i class="fa-solid fa-image me-2"></i> Manage Images
                </a>

                <a href="index.php?controller=Item&action=delete_item&param1=<?= $item->id ?>"
                  class="btn btn-outline-danger text-start">
                  <i class="fa-solid fa-trash me-2"></i> Delete Item
                </a>
              </div>
            </div>
          </div>
        <?php } elseif ($current_user === $item->owner_name && ($item->has_bids)) { ?>
          <div class="card text-bg-dark mb-3 border-light">
            <div class="card-body">
              <h5>Vous ne pouvez plus modifier les paramètres de votre objet car il existe des enchères dessus</h5>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
  </div>
  <div class="container-fluid px-4 pb-4 pt-0" style="margin-top: 0px; margin-bottom: 5px;">
    <div class="row">
      <div class="col-md-8">
        <div class="card text-bg-dark mb-3">
          <div class="card-header border-secondary d-flex justify-content-between align-items-center">
            <?php if ($item->has_bids) { ?>
              <span>Bid History</span>
              <span class="badge bg-light text-dark"><?= count($bids) ?> entries</span>
          </div>
          <ul class="list-group list-group-flush">
            <?php foreach ($bids as $bid): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-bold"><?= $bid->owner_pseudo ?></div>
                  <small class="text-secondary"><?= $bid->created_at->format('d/m/Y H:i') ?></small>
                </div>

                <span class="<?= $bid->item ? 'text-success fw-bold' : 'text-success' ?>">
                  € <?= $bid->amount ?>
                </span>

              </li>
            <?php endforeach ?>
          </ul>
        <?php } else { ?>
          <div class="alert alert-info text-center mt-3">
            Be the first to bet on this item ! <br>
            <strong>Starting price : € <?= $item->starting_bid ?></strong>
          </div>
        <?php } ?>

        </div>
      </div>
    </div>
  </div>
  <div class="fixed-bottom">

    <nav class="navbar navbar-dark bg-dark">
      <ul class="navbar-nav bottom-nav" id="nav-icons">
        <li class="nav-item text-center">
          <a href="browseItems/index" class="nav-link"><i class="fa-solid fa-magnifying-glass d-block"></i><small style="font-size: 10px;">Browse</small></a>
        </li>
        <li class="nav-item text-center">
          <a href="index.php?controller=Item&action=list" class="nav-link"><i class="fa-solid fa-house d-block"></i><small style="font-size: 10px;">My Items</small></a>
        </li>
        <li class="nav-item text-center">
          <a href="item/add_edit_item" class="nav-link"><i class="fa-solid fa-plus d-block"></i><small style="font-size: 10px;">Add Offer</small></a>
        </li>
        <li class="nav-item text-center">
          <a href="user/profile" class="nav-link"><i class="fa-solid fa-gear d-block"></i><small style="font-size: 10px;">Profile</small></a>
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