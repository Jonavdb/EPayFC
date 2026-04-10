$(document).ready(function () {
    // on attend que le document soit prêt
    const item_id = $("#js_carousel_1").data("item-id");
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

            $("#carousel-inner").append(
                // dans le corps du carrousel, on ajoute celui qui est active
                `<div class="carousel-item ${active}"> 
                <img src="${url}" class="d-block w-100" style="max-height:500px; object-fit:cover;">
                </div>`,
            );

            $("#js_thumbnails").append(
                // on ajoute les images de Thumbnails
                `<img src="${url}" class="rounded border border-secondary vignette-js" data-index="${index}" style="width:100px; height:70px; object-fit:cover; cursor:pointer;">`,
            );
        });
        $(".vignette-js").on("click", function () {
            // quand on clique sur une vignette
            const index = $(this).data("index"); // on récupère l'index de la vignette sur laquelle on a cliqué
            $("#itemCarousel").carousel(index); // on demande au carousel d'afficher celle-là
        });
    });
});
