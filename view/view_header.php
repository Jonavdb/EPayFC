<header class="content-box">
    <nav class="navbar fixed-top navbar-dark bg-dark border-bottom shadow-lg">
        <div class="container-fluid p-2">

            <div class="d-flex align-items-center" style="min-width: 50px;">
                <a href="<?= GoBack::go_back_2() ?>" class="btn-icon">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>

            <div class="flex-grow-1 text-center mx-auto">
                <div class="fw-bold fs-2 text-primary">
                    <?= $page_title ?? 'EpayFC' ?>

                    <?php if (!isset($hide_default_icon) || !$hide_default_icon): ?>
                        <i class="fa fa-shopping-cart ms-2"></i>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex justify-content-end align-items-center" style="min-width: 50px;">
                <?= $header_right_content ?? '' ?>
            </div>

        </div>
    </nav>
</header>