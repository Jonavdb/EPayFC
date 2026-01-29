<?php
$nav_title = $navbar_title ?? 'Application'; // Titre par défaut
$nav_icon  = $navbar_icon ?? '';             // Icône par défaut (vide)
$nav_back  = $navbar_back ?? 'javascript:history.back()'; // Retour arrière par défaut
?>

<header class="content-box">
    <nav class="navbar fixed-top navbar-dark bg-dark border-bottom shadow-lg">
        <div class="container-fluid p-2">

            <div class="flex-grow-1 text-start" style="max-width: 5%;">
                <a href="<?= $nav_back ?>" class="btn-icon">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>

            <div class="mx-auto">
                <div class="fw-bold fs-2 text-primary">
                    <?= $nav_title ?>
                    <?php if (!empty($nav_icon)): ?>
                        <i class="<?= $nav_icon ?>"></i>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </nav>
</header>