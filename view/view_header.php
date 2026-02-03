<header class="content-box">
    <nav class="navbar fixed-top navbar-dark bg-dark border-bottom shadow-lg">
        <div class="container-fluid p-2">

            <div class="flex-grow-1 text-start" style="max-width: 5%;">
                <a href="<?= GoBack::go_back() ?>" class="btn-icon">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>

            <div class="mx-auto">
                <div class="fw-bold fs-2 text-primary">
                    <?= $page_title ?? 'EpayFC' ?>
                    <i class="fa fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </nav>
</header>