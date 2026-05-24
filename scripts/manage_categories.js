//import { fetchConstants } from "./get_constants.js";

//const APP_CONFIG = await fetchConstants();

let deleteBtn;

$(function () {
    // on cache la version sans javascript
    $(".no-js-element").addClass("d-none");

    // on affiche la version avec javascript
    $(".js-read-display").removeClass("d-none");
    $(".js-edit-input").addClass("d-none");
    $(".js-hide-on-load").addClass("d-none");

    // on recup la liste de tous les boutons delete
    const deleteBtns = $(".delete-btn");

    // drag and drop logique
    // 1. Find the container we just built
    const sortableList = document.getElementById("sortable-category-list");

    // 2. Turn on the physics engine
    if (sortableList) {
        Sortable.create(sortableList, {
            handle: ".drag-handle", // Only allow dragging from the specific icon
            animation: 150, // Smooth sliding animation (ms)

            // This fires the exact moment the user drops the row
            onEnd: function (evt) {
                console.log("Drag ended! Time to save the new order.");
                // We will write the Phase 2 logic here next!
            },
        });
    }

    // on attache a chacun un évènement onClick
    deleteBtns.on("click", handleDeleteConfirmation);
});

function handleDeleteConfirmation(e) {
    e.preventDefault();

    // on récupère l'id de la catégorie qui est attaché au bouton "delete" cliqué
    const catId = $(e.currentTarget).data("id");

    // on affiche la box de confirmation en lui passant l'id
    askConfirm(catId);
}

function askConfirm(catId) {
    $("#unsavedModal").modal("show");
    $("#confirmLeaveBtn")
        .off("click")

        // ce qui se passe quand on appuie sur "yes"
        .on("click", async () => {
            try {
                // on envoie l'id au serveur
                const data = await $.post("category/delete_category_service", {
                    id: catId,
                });

                if (data.success) {
                    // réponse positive, on efface la ligne de la catégorie dans le html
                    $("input[name='id'][value='" + catId + "']")
                        .closest(".category-row")
                        .remove();
                } else {
                    alert(data.response); // on affiche la réponse du serveur
                }
            } catch (error) {
                console.error("AJAX request failed", error);
            }

            // on cache la box de confirmation
            $("#unsavedModal").modal("hide");
        });
}
