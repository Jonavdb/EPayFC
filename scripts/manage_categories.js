let errSave;

$(function () {
    // on cache la version sans javascript
    $(".no-js-element").addClass("d-none");

    // on affiche la version avec javascript
    $(".js-read-display").removeClass("d-none");
    $(".js-edit-input").addClass("d-none");
    $(".js-edit-input input").prop("disabled", false);
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

    // on recup la liste de tous les input de categorie
    const inputs = $(".js-edit-input input");
    //errSave = $("#errSave");

    // ..
    inputs.bind("blur", checkNameValid);

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
    // --- 1. RÈGLE DU HIGHLANDER : Fermer et réinitialiser les autres ---

    // Restaurer le nom d'origine dans tous les inputs
    $(".category-row").each(function () {
        const row = $(this);
        const safeName = row.find(".name-only").text().trim();
        row.find(".js-edit-input input").val(safeName);
    });

    // Nettoyer l'UI globalement (on enchaîne les méthodes jQuery pour la performance)
    $(".js-edit-input input").prop("disabled", true).removeClass("is-invalid is-valid").addClass("border-secondary");

    // Grouper les sélecteurs pour cacher/afficher en une seule ligne
    $(".js-edit-input, .js-edit-btns").addClass("d-none");
    $(".js-read-display").removeClass("d-none");
    $(".err-save-container").text("");

    // --- 2. OUVRIR LA RANGÉE CIBLÉE ---
    const parentRow = $(e.currentTarget).closest(".category-row");
    parentRow.find(".js-read-display").addClass("d-none");
    parentRow.find(".js-edit-input").removeClass("d-none");
    parentRow.find(".js-edit-input input").prop("disabled", false).focus();
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

// 1. MUST declare 'async' to use 'await' inside
async function checkNameValid(e) {
    // --- 1. INITIALISATION ---
    const targetInput = $(e.currentTarget);
    const val = targetInput.val().trim();
    const id = targetInput.data("id");

    // Astuce : Cibler la rangée parente une seule fois simplifie tout le reste !
    const row = targetInput.closest(".category-row");
    const errSave = row.find(".err-save-container");
    const oldName = row.find(".name-only").text().trim();

    // Fonction utilitaire pour refermer la ligne
    const closeEditMode = () => {
        targetInput.prop("disabled", true);
        row.find(".js-edit-input, .js-edit-btns").addClass("d-none");
        row.find(".js-read-display").removeClass("d-none");
    };

    // --- 2. SORTIE SILENCIEUSE ---
    if (val === oldName) {
        setFieldState(targetInput, errSave, null);
        return closeEditMode();
    }

    // --- 3. VALIDATION FRONT-END (Early returns = code plus court) ---
    if (val === "") return setFieldState(targetInput, errSave, false, "Champ vide.");

    if (val.length < 3 || val.length > 25) return setFieldState(targetInput, errSave, false, "Le nom doit contenir entre 3 et 25 caractères.");

    if (!/^[A-Z][a-zA-Z]*$/.test(val))
        return setFieldState(targetInput, errSave, false, "Format invalide : commencez par une majuscule, sans espaces ni chiffres.");

    // --- 4. REQUÊTES SERVEUR (Un seul bloc Try/Catch) ---
    try {
        // A. Vérification de l'unicité
        const uniqueCheck = await $.post("category/check_unique_service", { name: val }, null, "json");

        if (!uniqueCheck?.success) {
            return setFieldState(targetInput, errSave, false, uniqueCheck.response);
        }

        // B. Si unique, on sauvegarde
        const saveResult = await $.post("category/save_name_service", { name: val, id: id }, null, "json");
        setFieldState(targetInput, errSave, saveResult.success, saveResult.response);

        // C. Succès : mise à jour visuelle
        if (saveResult.success) {
            row.find(".name-only").text(val);
            closeEditMode();
        }
    } catch (error) {
        console.error("Erreur AJAX :", error);
        setFieldState(targetInput, errSave, false, "Erreur de communication avec le serveur.");
    }
}

/* FONCTION UTILITAIRE : gère l'affichage visuel des erreurs */
function setFieldState(field, errField, isValid, errMsg = "") {
    field.removeClass("is-valid is-invalid border-secondary");
    errField.removeClass("text-danger").html("");

    if (isValid === true) {
        field.addClass("is-valid");
    } else if (isValid === false) {
        field.addClass("is-invalid");
        errField.html(`<ul><li>${errMsg}</li></ul>`).addClass("text-danger small mt-1");
    } else {
        field.addClass("border-secondary"); // État neutre
    }
}
