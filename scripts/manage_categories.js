//import { fetchConstants } from "./get_constants.js";

//const APP_CONFIG = await fetchConstants();

let deleteBtn;

$(function () {
    // on cache la version sans javascript
    $(".no-js-element").addClass("d-none");

    // on affiche la version avec javascript
    $(".js-read-display").removeClass("d-none");
    $(".js-edit-input").addClass("d-none");
    $(".js-edit-input").prop("disabled", false);
    $(".js-hide-on-load").addClass("d-none");

    // on recup la liste de tous les boutons delete
    const deleteBtns = $(".delete-btn");

    // on attache a chacun un évènement onClick
    deleteBtns.on("click", handleDeleteConfirmation);

    // on recup la liste de tous les boutons edit
    const editBtns = $(".edit-btn");

    // on attache a chacun un évènement onClick
    editBtns.on("click", handleEditCategory);

    // on récupère le bouton d'ajout d'une nouvelle categorie
    const addBtn = $(".add-btn");
    addBtn.on("click", handleAddCategory);

    // on récupère le bouton cancel d'un ajout d'une categorie
    const cancelBtn = $(".cancel-btn");
    cancelBtn.on("click", handleCancelAddCategory);

    // on setup l'effet drag and drop
    const sortableList = document.getElementById("sortable-category-list");
    if (sortableList) {
        Sortable.create(sortableList, {
            handle: ".drag-handle", // on autorise uniquement le drag pour éléments de cette classe
            animation: 150, // vitesse animation de swap

            // trigger au moment ou on drop l'élément
            onEnd: function (evt) {
                console.log("Drag ended! Time to save the new order.");

                // on récupère TOUTES les rangées
                const categoryRows = $(".category-row");

                // on crée une nouvelle liste d'id après le drop pour mettre à jour l'ordre
                const newOrder = [];
                categoryRows.each((index, row) => {
                    // on récupère l'id de la rangée
                    const id = $(row).data("id");
                    // on ajoute l'id à la nouvelle liste
                    newOrder.push(id);
                });

                console.log("Nouvel ordre envoyé :", newOrder);

                $.post(
                    "category/update_order_categories_service",
                    { newOrder: newOrder },
                    // si pas d'erreur reçue du serveur
                    function (data) {
                        if (data.success) {
                            console.log(data.response);
                        } else {
                            // Échec de la synchronisation (les listes ne correspondaient pas)
                            console.error(data.response);
                            alert("Erreur de sauvegarde : " + data.response);

                            // Le filet de sécurité : on annule l'illusion visuelle en rechargeant la page
                            window.location.reload();
                        }
                    },
                    "json",
                ).fail(function () {
                    // Si le serveur plante (Erreur 500) ou si la connexion internet coupe
                    alert("Erreur critique de communication avec le serveur.");
                    window.location.reload();
                });
            },
        });
    }
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

function handleEditCategory(e) {
    e.preventDefault();

    // on récupère le bouton sur lequel on a cliqu"
    const clickedBtn = $(e.currentTarget);

    // on récupère le parent 'category-row' ie la rangée entière
    const parentRow = clickedBtn.closest(".category-row");

    // on met toutes les autres rangées en mode read
    $(".js-edit-input").addClass("d-none");
    $(".js-read-display").removeClass("d-none");

    // on met la rangée selectionnée en mode ecriture
    parentRow.find(".js-read-display").addClass("d-none");
    parentRow.find(".js-edit-input").removeClass("d-none");
}

function handleAddCategory(e) {
    e.preventDefault();

    // on récupère le bouton sur lequel on a cliqué
    const addBtn = $(e.currentTarget);

    // on récupère l'input associé
    const input = addBtn.siblings(".js-hide-on-load");

    // on met la rangée selectionnée en mode ecriture
    input.removeClass("d-none");
}

function handleCancelAddCategory(e) {
    e.preventDefault();

    // on récupère le bouton sur lequel on a cliqué
    const cancelBtn = $(e.currentTarget);

    // on récupère l'input associé
    const input = cancelBtn.siblings(".js-hide-on-load");

    // on remet la rangée selectionnée en mode lecture
    input.addClass("d-none");
    cancelBtn.addClass("d-none");
}
