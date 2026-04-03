<div class="col">
    <a href="<?= $element->href ?>" class="card h-100 text-decoration-none shadow-sm card-hover <?= ($element->card_context !== 'my-items') ? 'dark-theme' : '' ?>">
        <div class="position-relative">
            <img src="<?= $element->img_src ?>" class="card-img-top" alt="<?= htmlspecialchars($element->raw_item->get_title()) ?>" style="height: 200px; object-fit: cover;">

            <div class="card-img-overlay d-flex flex-column justify-content-between p-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <?php if ($element->card_context === 'my-items'): ?>
                            <?php if ($element->context === 'sold'): ?>
                                <span class="badge rounded-pill bg-success"><i class="fa-solid fa-check"></i> Sold</span>
                            <?php elseif ($element->context === 'unsold'): ?>
                                <span class="badge rounded-pill bg-secondary"><i class="fa-solid fa-ban"></i> Unsold</span>
                            <?php endif; ?>
                        <?php elseif ($element->card_context === 'browse-bidding'): ?>
                            <?php if ($element->is_highest_bidder): ?>
                                <span class="badge rounded-pill bg-success badge-bidder d-inline-flex align-items-center px-3 py-2">
                                    <i class="fa-solid fa-trophy me-2"></i> Highest Bidder
                                </span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-dark badge-bidder d-inline-flex align-items-center px-3 py-2">
                                    <i class="fa-solid fa-gavel flipped-horizontal me-2"></i> Bidder
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        <?php if ($element->raw_item->get_is_auction()): ?>
                            <span class="badge rounded-pill bg-warning text-dark"><i class="fa-solid fa-gavel fa-flip-horizontal"></i> Auction</span>
                        <?php endif; ?>
                        <?php if ($element->raw_item->get_has_buy_now() && !$element->raw_item->get_buy_now_reached()): ?>
                            <span class="badge rounded-pill bg-primary d-inline-flex align-items-center px-3 py-2">
                                <i class="fa-solid fa-bag-shopping me-2"></i> Buy now
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($element->extra_images > 0): ?>
                    <div class="d-flex justify-content-end">
                        <span class="badge rounded-pill text-bg-dark bg-opacity-75"><i class="fa-solid fa-images"></i> <?= $element->extra_images + 1 ?> image<?= $element->extra_images > 1 ? 's' : '' ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body d-flex flex-column">
            <div class="mb-2">
                <div class="text-start card-title text-truncate fw-bold fs-5 mb-0 text-white">
                    <?= htmlspecialchars($element->raw_item->get_title()) ?>
                </div>
                <small class="text-secondary">by <?= $element->owner ?></small>
            </div>

            <div class="d-flex justify-content-between align-items-start mt-auto mb-2 pt-2">
                <div class="d-flex flex-column">
                    <?php if ($element->price_display !== null): ?>
                        <div class="fs-3 fw-bold <?= $element->price_color ?>">
                            € <?= $element->price_display ?>
                        </div>
                    <?php else: ?>
                        <div class="fs-3 fw-bold">&nbsp;</div>
                    <?php endif; ?>

                    <div class="small <?= $element->time_color ?> mt-2">
                        <i class="fa-regular fa-clock"></i> <?= $element->time_left ?>
                    </div>
                </div>

                <?php if ($element->show_current_bid && $element->context !== 'unsold'): ?>
                    <div class="text-end">
                        <div class="text-secondary small mb-0">Current bid</div>
                        <div class="fs-3 fw-bold text-success">
                            € <?= $element->current_bid_display ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </a>
</div>