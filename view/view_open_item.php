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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <base href="<?= $web_root ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Jonathan_Vanderbiest">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="css/styles.css" rel="stylesheet" type="text/css" />
  <title>Item open</title>
</head>

<body class="d-flex flex-column min-vh-100 dark-bg" style="padding-bottom: 140px;">

  <?php
  $page_title = "Item open";
  include 'view/view_header.php';
  ?>

  <div class="container-fluid px-4" style="padding-top: 80px;">
    <div class="row g-4">
      <div class="col-md-8">

        <div class="card text-bg-dark border-secondary mb-4">
          <img src="<?= $image_to_display ?>" class="card-img-top" alt="<?= $item->title ?>" style="max-height: 500px; object-fit: cover;">

          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h2 class="card-title fw-bold text-white mb-0"><?= $item->title ?></h2>
              <?php if ($item->is_direct_sale): ?>
                <span class="badge bg-secondary">Direct sale</span>
              <?php else: ?>
                <span class="badge bg-secondary">Auction</span>
              <?php endif; ?>
            </div>

            <p class="card-text text-secondary mb-4"><?= $item->description ?></p>

            <div class="small text-secondary">
              <p class="mb-1">Start: <span class="fw-bold"><?= $item->created_at->format('d/m/Y H:i:s') ?></span></p>
              <p class="mb-1">Ends: <span class="fw-bold"><?= $item->end_at->format('d/m/Y H:i:s') ?></span> </p>
            </div>
          </div>
        </div>

        <h3 class="text-light mt-4 mb-4 fs-4 fw-bold text-start">Additional Images</h3>
        <div class="d-flex gap-3 mb-5">
          <?php
          $origin = 'from-browse';
          $uri = $_SERVER['REQUEST_URI'];
          if (strpos($uri, 'from-list') !== false) $origin = 'from-list';
          elseif (strpos($uri, 'from-profile') !== false) $origin = 'from-profile';
          elseif (strpos($uri, 'from-items') !== false) $origin = 'from-items';

          foreach ($item->image_urls as $index => $image):
            $is_active = ($image === $image_to_display);
            $border_class = $is_active ? 'border-primary' : 'border-secondary';
          ?>
            <a href="item/open/<?= $item->id ?>/<?= $index ?>/<?= $origin ?>" class="d-block">
              <img src="<?= $image ?>"
                class="rounded border <?= $border_class ?>"
                alt="View <?= $index ?>"
                style="width: 100px; height: 70px; object-fit: cover;">
            </a>
          <?php endforeach; ?>
        </div>

        <?php if ($item->has_bids): ?>
          <div class="card text-bg-dark border-secondary">
            <div class="card-header border-secondary d-flex justify-content-between align-items-center bg-dark bg-opacity-50 py-3">
              <span class="fw-bold">Bid History</span>
              <span class="badge bg-light text-dark rounded-pill"><?= count($bids) ?> entries</span>
            </div>
            <ul class="list-group list-group-flush">
              <?php foreach ($bids as $bid): ?>
                <li class="list-group-item bg-transparent border-secondary text-white d-flex justify-content-between align-items-center py-3">
                  <div>
                    <div class="fw-bold mb-1"><?= $bid->owner_pseudo ?></div>
                    <small class="text-secondary"><?= $bid->created_at->format('d/m/Y H:i:s') ?></small>
                  </div>
                  <div class="fw-bold text-success fs-5">
                    € <?= number_format($bid->amount, 2, ',', ' ') ?>
                  </div>
                </li>
              <?php endforeach ?>
            </ul>
          </div>
        <?php elseif ($item->owner_name !== $current_user): ?>
          <div class="card text-bg-dark border-secondary">
            <div class="text-center mt-3 mb-3">
              <?php if ($item->is_direct_sale) { ?>
                <h4 class="card-title mb-3">Buy this item for just : <span class="fw-bold text-success"><?= $item->buy_now_price ?>€</h4>
              <?php } else { ?>
                <h4 class="card-title mb-3">Be the first to bet on this item !</h4>
                <h4 class="card-text fs-5">
                  Starting price : <span class="fw-bold text-success">€ <?= $item->starting_bid ?></span>
                </h4>
              <?php } ?>
            </div>
          </div>
        <?php endif; ?>


      </div>

      <div class="col-md-4">
        <div class="card text-bg-dark border-secondary mb-4">
          <div class="card-body p-4">
            <h4 class="fw-bold text-secondary mb-3 text-start fs-4">Pricing</h4>

            <?php if (!$item->is_direct_sale && $item->has_bids): ?>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold">Current Bid</span>
                <span class="text-success fw-bold fs-2">€ <?= number_format($item->current_bid, 2, ',', ' ') ?></span>
              </div>
            <?php endif; ?>

            <?php if (!$item->is_direct_sale && !$item->has_bids): ?>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold">Starting bid</span>
                <span class="text-success fw-bold fs-2">€ <?= number_format($item->current_bid, 2, ',', ' ') ?></span>
              </div>
            <?php endif; ?>

            <?php if ($item->has_buy_now): ?>
              <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="fw-bold">Buy Now Price</span>
                <span class="text-white fw-bold">€ <?= number_format($item->buy_now_price, 2, ',', ' ') ?></span>
              </div>
            <?php endif; ?>

            <?php if ($auction_finished): ?>
              <?php if ($winner): ?>
                <?php if ($winner === $current_user): ?>
                  <div class="alert alert-success mt-3">You won this item!</div>
                <?php else: ?>
                  <div class="alert alert-warning mt-3">Sold to <?= $winner ?></div>
                <?php endif; ?>
              <?php else: ?>
                <div class="alert alert-secondary mt-3">Auction ended without bids.</div>
              <?php endif; ?>

            <?php elseif ($current_user): ?>
              <?php if (!$item->is_direct_sale): ?>
                <form action="index.php?controller=item&action=new_bid" method="POST" class="mb-3">
                  <input type="hidden" name="item_id" value="<?= $item->id ?>">

                  <div class="input-group mb-2">
                    <span class="input-group-text bg-dark border-secondary text-secondary">€</span>
                    <input type="number"
                      class="form-control bg-dark border-secondary text-white"
                      name="amount"
                      value="<?= $item->current_bid + 1 ?>"
                      min="<?= $item->current_bid + 1 ?>"
                      required
                      <?php if ($current_user === $item->owner_name) echo 'disabled'; ?>>
                  </div>

                  <button class="btn btn-success w-100 fw-bold py-2" type="submit" <?php if ($current_user === $item->owner_name) echo 'disabled'; ?>>
                    Place Bid
                  </button>
                </form>
              <?php endif; ?>

              <?php if ($item->has_buy_now): ?>
                <form action="index.php?controller=item&action=buy_now" method="POST" class="p-1 border-0">
                  <input type="hidden" name="item_id" value="<?= $item->id ?>">
                  <button class="btn btn-primary w-100 fw-bold py-2" type="submit" <?php if ($current_user === $item->owner_name) echo 'disabled'; ?>>
                    Buy Now at € <?= number_format($item->buy_now_price, 2, ',', ' ') ?>
                  </button>
                </form>
              <?php endif; ?>

              <?php if ($current_user === $item->owner_name): ?>
                <div class="text-danger small mt-4 text-center">You cannot bid on your own item.</div>
              <?php endif; ?>

            <?php else: ?>
              <div class="alert alert-info mt-3">Please <a href="main/login">login</a> to bid.</div>
            <?php endif; ?>
          </div>
        </div>

        <div class="card text-bg-dark border-secondary mb-4">
          <div class="card-body p-4">
            <h4 class="fw-bold text-secondary mb-3 text-start fs-4">Seller Information</h4>
            <div class="d-flex align-items-center">
              <img src="<?= $item->owner_picture ?>" alt="User" class="rounded-circle me-3 bg-secondary" style="width: 48px; height: 48px; object-fit: cover;">
              <div>
                <div class="fw-bold text-white"><?= $item->owner_name ?></div>
                <div class="text-secondary small"><?= ucfirst($item->owner_role) ?></div>
              </div>
            </div>
          </div>
        </div>

        <?php if ($current_user === $item->owner_name && (!$item->has_bids)): ?>
          <div class="card text-bg-dark border-secondary">
            <div class="card-body p-4">
              <h4 class="fw-bold text-secondary mb-3 fs-4 text-start">Manage Your Item</h4>
              <div class="d-grid gap-2">
                <a href="item/edit/<?= $item->id ?>" class="btn btn-outline-secondary border text-decoration-none"><i class="fa-solid fa-pen me-2"></i> Edit</a>
                <a href="item/manage_images/<?= $item->id ?>" class="btn btn-outline-info border text-decoration-none"><i class="fa-solid fa-image me-2"></i> Images</a>
                <a href="item/delete/<?= $item->id ?>" class="btn btn-outline-danger border text-decoration-none"><i class="fa-solid fa-trash me-2"></i> Delete</a>
              </div>
            </div>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>

  <footer class="fixed-bottom">
    <?php include 'view/view_timer.php'; ?>
    <?php include 'view/view_footer.php'; ?>
  </footer>

</body>

</html>