<nav class="navbar navbar-dark bg-dark border-top shadow-lg">
    <div class="container-fluid">
        <div class="d-flex justify-content-around w-100 py-3">

            <a class="nav-link d-flex flex-column align-items-center text-secondary" href="item/browse/">
                <i class="fa-solid fa-magnifying-glass-plus fs-4 mb-1"></i>
                <span>Browse</span>
            </a>

            <?php if ($is_guest): ?>
                <a class="nav-link d-flex flex-column align-items-center text-secondary" href="user/signup">
                    <i class="fa-solid fa-file-signature fs-4 mb-1"></i>
                    <span>Join us</span>
                </a>
            <?php else: ?>
                <a class="nav-link d-flex flex-column align-items-center text-secondary" href="item/list">
                    <i class="fas fa-home fs-4 mb-1"></i>
                    <span>My Items</span>
                </a>
            <?php endif; ?>

            <?php if (!$is_guest): ?>
                <a class="nav-link d-flex flex-column align-items-center text-secondary" href="item/add">
                    <i class="fa-solid fa-plus fs-4 mb-1"></i>
                    <span>Add Offer</span>
                </a>

                <a class="nav-link d-flex flex-column align-items-center text-secondary" href="user/profile">
                    <i class="fa-solid fa-gear fs-4 mb-1"></i>
                    <span>Profile</span>
                </a>
            <?php endif; ?>

        </div>
    </div>
</nav>