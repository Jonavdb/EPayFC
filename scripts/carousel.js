$(document).ready(function () {
    // on attend que le document soit prêt
    const item_id = $("#js_carousel_1").data("item-id");
    function updateThumbnailsBorder(selectedIndex) {
        const $thumbnails = $(".vignette-js"); // on récupère toutes les vignettes
        $thumbnails.removeClass("border-primary").addClass("border-secondary"); // on enleve la bordure primaire à toutes les vignettes et on met la bordure secondaire
        const $activeThumbnail = $thumbnails.filter(
            `[data-index="${selectedIndex}"]`, // on filtre les vignettes pour trouver celle qui est séléctionnée
        );
        $activeThumbnail
            .removeClass("border-secondary")
            .addClass("border-primary");
    }
    $.get("item/get_js_images/" + item_id, {}, function (images) {
        // on appelle la fonction get_js_images du controller qui nous renvoie du Json
        if (images.length === 0) {
            // si pas d'images, on fair return et on continue pas le script
            return;
        }
        $("#no_js_main_image").hide(); // on cache la version no_js
        $("#no_js_thumbnails").hide();
        $("#js_carousel_1").show(); // on affiche la version js
        $("#js_carousel_2").show();

        $.each(images, function (index, url) {
            // pour chaque image du Json
            const active = index === 0 ? "active" : ""; // si index 0 du tableau Json, on set en active sinon rien
            const borderClass =
                index === 0 ? "border-primary" : "border-secondary"; // même chose pour la bordure des vignettes

            $("#carousel-inner").append(
                // dans le corps du carrousel, on ajoute celui qui est active
                `<div class="carousel-item ${active}">
                    <div class="mx-auto" style="max-width: 1080px;">
                        <div class="ratio ratio-16x9 bg-white"> 
                            <img src="${url}" class="d-block w-100" style="object-fit: contain;">
                        </div>
                    </div>        
                </div>`,
            );

            $("#js_thumbnails").append(
                // on ajoute les images de Thumbnails
                `<img src="${url}" class="rounded border border-2 ${borderClass} vignette-js" data-index="${index}" style="width:100px; height:70px; object-fit:cover; cursor:pointer;">`,
            );
        });
        $(".vignette-js").on("click", function () {
            // quand on clique sur une vignette
            const index = $(this).data("index"); // on récupère l'index de la vignette sur laquelle on a cliqué
            $("#itemCarousel").carousel(index); // on demande au carousel d'afficher celle-là
            updateThumbnailsBorder(index); // on met à jour les bordures des vignettes
        });

        $("#itemCarousel").on("slide.bs.carousel", function (e) {
            updateThumbnailsBorder(e.to); // quand on slide le carousel, on remet à jour les bordures
        });
    });
});
