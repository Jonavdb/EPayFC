<div class="col">
    <a href="item/open/<?= $element->raw_item->id ?>/from-items" class="card h-100 text-decoration-none shadow-sm card-hover">
        <div class="position-relative">
            <img src="<?= $element->img_src ?>" class="card-img-top" alt="<?= htmlspecialchars($element->raw_item->title) ?>" style="height: 200px; object-fit: cover;">

            <div class="card-img-overlay d-flex flex-column justify-content-between p-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <?php if ($element->context === 'sold'): ?>
                            <span class="badge rounded-pill bg-success"><i class="fa-solid fa-check"></i> Sold</span>
                        <?php elseif ($element->context === 'unsold'): ?>
                            <span class="badge rounded-pill bg-secondary"><i class="fa-solid fa-ban"></i> Unsold</span>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        <?php if ($element->raw_item->is_auction): ?>
                            <span class="badge rounded-pill bg-warning text-dark"><i class="fa-solid fa-gavel fa-flip-horizontal"></i> Auction</span>
                        <?php endif; ?>
                        <?php if ($element->raw_item->has_buy_now && !$element->raw_item->buy_now_reached): ?>
                            <span class="badge rounded-pill bg-primary"><i class="fa-solid fa-bag-shopping"></i> Buy now</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($element->extra_images > 0): ?>
                    <div class="d-flex justify-content-end">
                        <span class="badge rounded-pill text-bg-dark bg-opacity-75"><i class="fa-solid fa-images"></i> <?= $element->extra_images ?> image<?= $element->extra_images > 1 ? 's' : '' ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body d-flex flex-column">
            <div class="mb-2">
                <div class="text-start card-title text-truncate fw-bold fs-5 mb-0 text-white"><?= htmlspecialchars($element->raw_item->title) ?></div>
                <small class="text-secondary">by <?= htmlspecialchars($user->pseudo) ?></small>
            </div>

            <div class="d-flex justify-content-between align-items-start mt-auto mb-2 pt-2">
                <div class="d-flex flex-column">
                    <?php if ($element->raw_item->has_buy_now): ?>
                        <div class="fs-3 fw-bold text-primary">
                            € <?= number_format($element->raw_item->buy_now_price, 2, ',', '.') ?>
                        </div>
                    <?php else: ?>
                        <div class="fs-3 fw-bold">&nbsp;</div>
                    <?php endif; ?>

                    <?php
                    // Si le texte calculé par le controller est "Closed", on met en gris, sinon en vert
                    $time_color_class = ($element->time_left === 'Closed') ? 'text-secondary' : 'text-success';
                    ?>
                    <div class="small <?= $time_color_class ?> mt-2">
                        <i class="fa-regular fa-clock"></i> <?= $element->time_left ?>
                    </div>
                </div>

                <?php if ($element->raw_item->is_auction && $element->context !== "unsold"): ?>
                    <div class="text-end">
                        <div class="text-secondary small mb-0">Current bid</div>
                        <div class="fs-3 fw-bold text-success">
                            € <?= number_format($element->raw_item->current_bid, 2, ',', '.') ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </a>
</div>