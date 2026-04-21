import { fetchConstants } from "./get_constants.js";

let itemTitle,
    errTitle,
    description,
    errDescription,
    duration,
    errDuration,
    startingBid,
    errStartingBid,
    instantOptional,
    errInstant,
    direct,
    errDirect,
    submitBtn,
    goBackBtn,
    footerElements;

const APP_CONFIG = await fetchConstants();

$(function () {
    submitBtn = $("#submitBtn");
    goBackBtn = $("#goBack");
    footerElements = $("footer a.nav-link");

    itemTitle = $("#title");
    errTitle = $("#errTitle");

    description = $("#description");
    errDescription = $("#errDescription");

    duration = $("#duration");
    errDuration = $("#errDuration");

    startingBid = $("#starting_bid");
    errStartingBid = $("#errStartingBid");

    instantOptional = $("#instant");
    errInstant = $("#errInstant");

    direct = $("#direct");
    errDirect = $("#errDirect");

    // bindings
    itemTitle.bind("blur", checkTitleValid);
    description.bind("blur", checkDescriptionValid);
    duration.bind("blur", checkDurationValid);

    startingBid.bind("blur", checkStartingBidValid);
    startingBid.bind("blur", checkOptionalInstantValid);
    startingBid.bind("blur", checkDirectValid);

    instantOptional.bind("blur", checkOptionalInstantValid);
    instantOptional.bind("blur", checkStartingBidValid);
    instantOptional.bind("blur", checkDirectValid);

    direct.bind("blur", checkDirectValid);
    direct.bind("blur", checkStartingBidValid);
    direct.bind("blur", checkOptionalInstantValid);

    // event listener sur le go back
    goBackBtn.on("click", null, (e) => {
        handleLeaveConfirmation(e);
    });
    footerElements.on("click", null, (e) => {
        handleLeaveConfirmation(e);
    });

    // triggers de validation au chargement
    itemTitle.trigger("blur");
    startingBid.trigger("blur");
    description.trigger("blur");
    duration.trigger("blur");
    startingBid.trigger("blur");
    instantOptional.trigger("blur");
    direct.trigger("blur");

    // debug
    console.log("go back retrieved:" + goBackBtn.hasClass("btn-icon"));
});

async function checkTitleValid() {
    // on récupère les constantes dont on a besoin pour la validation
    const titleSanitized = itemTitle.val().trim();
    const lengthTitle = titleSanitized.length;
    const minSize = APP_CONFIG.min_item_length;
    const maxSize = APP_CONFIG.max_item_length;

    if (titleSanitized === "") {
        // champ laissé vide : affichage texte par défaut et on masque l'erreur
        errTitle.html("").removeClass("text-danger");
        itemTitle.removeClass("is-invalid is-valid").addClass("border-secondary");
        checkFormValidity();
        return;
    }

    let isValid = false;
    if (lengthTitle >= minSize && lengthTitle <= maxSize) {
        // check longueur valide
        isValid = true;
    }

    if (isValid) {
        // succès :
        errTitle.html("").removeClass("text-danger");
        itemTitle.removeClass("is-invalid border-secondary").addClass("is-valid");
    } else {
        // erreur : cadre rouge
        errTitle.html("<ul><li>Title length must be between 2 and 255.</li></ul>").addClass("text-danger small mt-1");
        itemTitle.removeClass("is-valid border-secondary").addClass("is-invalid");
    }

    checkFormValidity();
}

async function checkDescriptionValid() {
    // on récupère les constantes dont on a besoin pour la validation
    const descriptionSanitized = description.val().trim();
    const lengthDesc = descriptionSanitized.length;
    const minSize = APP_CONFIG.min_description_length;
    const maxSize = APP_CONFIG.max_description_length;

    if (descriptionSanitized === "") {
        // champ laissé vide : affichage texte par défaut et on masque l'erreur
        errDescription.html("").removeClass("text-danger");
        description.removeClass("is-invalid is-valid").addClass("border-secondary");
        checkFormValidity();
        return;
    }

    let isValid = false;
    if (lengthDesc >= minSize && lengthDesc <= maxSize) {
        // check longueur valide
        isValid = true;
    }

    if (isValid) {
        // succès : cadre vert
        errDescription.html("").removeClass("text-danger");
        description.removeClass("is-invalid border-secondary").addClass("is-valid");
    } else {
        // erreur cadre rouge
        errDescription.html("<ul><li>Description length must be between 3 and 65535 characters.</li></ul>").addClass("text-danger small mt-1");
        description.removeClass("is-valid border-secondary").addClass("is-invalid");
    }

    checkFormValidity();
}

async function checkDurationValid() {
    const minDurationDays = APP_CONFIG.min_duration_days;
    const maxDurationDays = APP_CONFIG.max_duration_days;

    if (duration.val().trim() === "") {
        // champ laissé vide : affichage text par défaut et on masque l'erreur
        errDuration.html("").removeClass("text-danger");
        duration.removeClass("is-invalid is-valid").addClass("border-secondary");
        checkFormValidity();
        return;
    }

    const durationValue = duration.val().trim() !== "" ? parseInt(duration.val().trim()) : null;
    let isValid = durationValue >= minDurationDays && durationValue <= maxDurationDays;

    if (isValid) {
        // succès :
        errDuration.html("").removeClass("text-danger");
        duration.removeClass("is-invalid border-secondary").addClass("is-valid");
    } else {
        // erreur : cadre rouge
        errDuration.html("<ul><li>Duration must be at least 1 day and at most 365 days.</li></ul>").addClass("text-danger small mt-1");
        duration.removeClass("is-valid").addClass("is-invalid");
    }

    checkFormValidity();
}

async function checkStartingBidValid() {
    if (startingBid.val().trim() === "") {
        // we keep it neutral if empty, but it's still invalid
        startingBid.removeClass("is-invalid is-valid").addClass("border-secondary");
        errStartingBid.html("").removeClass("text-danger");
        checkFormValidity();
        return;
    }

    // on récupère les données puis on normalize
    const sanitizedBid = parseFloat(startingBid.val().trim());
    const sanitizedInstant = instantOptional.val().trim() !== "" ? parseFloat(instantOptional.val().trim()) : null;
    const sanitizedDirect = direct.val().trim() !== "" ? parseFloat(direct.val().trim()) : null;

    // on vérifie si les valeurs ont bien été normalizées
    const startingBidExists = true; // vrai par défaut puisqu'on a passé la première condition
    const instantOptionalExists = sanitizedInstant !== null;
    const directExists = sanitizedDirect !== null;

    // erreur pas de start bid ni de direct price
    const noneExists = !startingBidExists && !directExists;

    // erreur quand on essaie de remplir starting bid et direct price
    const bothExists = startingBidExists && directExists;

    // erreur quand starting bid trop faible
    const minimumRequired = startingBidExists && !directExists && sanitizedBid <= 0;

    // erreur si prix optionnel est inférieur au starting bid
    const instantPriceLower = startingBidExists && !directExists && instantOptionalExists && sanitizedInstant <= sanitizedBid;

    // on clear l'état du champ
    startingBid.removeClass("is-invalid is-valid border-secondary");
    errStartingBid.html("").removeClass("text-danger");

    if (bothExists) {
        errStartingBid.html("<ul><li>You can't put a direct sale price and a starting bid.</li></ul>").addClass("text-danger");
        startingBid.addClass("is-invalid");
    } else if (minimumRequired) {
        // erreur cadre rouge
        errStartingBid.html("<ul><li>Starting bid minimum 1.</li></ul>").addClass("text-danger");
        startingBid.addClass("is-invalid");
    } else if (instantPriceLower) {
        // erreur cadre rouge
        errStartingBid.html("<ul><li>Instant purchase price must be higher than starting bid price.</li></ul>").addClass("text-danger");
        startingBid.addClass("is-invalid");
    } else if (noneExists) {
        // erreur cadre rouge
        errStartingBid.html("<ul><li>You must either fill a starting bid or a direct sale price.</li></ul>").addClass("text-danger");
        startingBid.addClass("is-invalid");
    } else {
        // si pas d'erreur, alors valide
        // success : cadre vert
        startingBid.addClass("is-valid");
    }

    checkFormValidity();
}

async function checkOptionalInstantValid() {
    // we keep it neutral if empty, but it's still invalid
    if (instantOptional.val().trim() === "") {
        instantOptional.removeClass("is-invalid is-valid").addClass("border-secondary");
        errInstant.html("").removeClass("text-danger");
        checkFormValidity();
        return;
    }

    // on récupère les données puis on normalize
    const sanitizedBid = startingBid.val().trim() !== "" ? parseFloat(startingBid.val().trim()) : null;
    const sanitizedInstant = parseFloat(instantOptional.val().trim());
    const sanitizedDirect = direct.val().trim() !== "" ? parseFloat(direct.val().trim()) : null;

    // on vérifie si les valeurs ont bien été normalizées
    const startingBidExists = sanitizedBid !== null;
    const instantOptionalExists = true; // vrai par défaut puisqu'on a passé la première condition
    const directExists = sanitizedDirect !== null;

    // neutre si le champ est vide
    const neutral = instantOptionalExists === false;

    // erreur si prix direct est déjà renseigné
    const bothExists = directExists === true;

    // erreur si prix optionnel sans start bid
    const noStartBid = startingBidExists === false;

    // erreur si valeur trop faible
    const minimumRequired = sanitizedInstant < 1;

    // erreur si prix optionnel plus petit que start bid
    const instantPriceLower = sanitizedInstant <= sanitizedBid;

    // on clear l'état du champ
    instantOptional.removeClass("is-invalid is-valid border-secondary");
    errInstant.html("").removeClass("text-danger");

    if (neutral) {
        // nothing
        errInstant.html("").removeClass("text-danger");
        instantOptional.removeClass("is-invalid is-valid").addClass("border-secondary");
    } else if (bothExists) {
        // erreur cadre rouge
        errInstant.html("<ul><li>You can't put a direct sale price and an optional instant price.</li></ul>").addClass("text-danger");
        instantOptional.addClass("is-invalid");
    } else if (noStartBid) {
        // erreur cadre rouge
        errInstant.html("<ul><li>You need a starting bid.</li></ul>").addClass("text-danger");
        instantOptional.addClass("is-invalid");
    } else if (minimumRequired) {
        // erreur cadre rouge
        errInstant.html("<ul><li>Minimum value 1.</li></ul>").addClass("text-danger");
        instantOptional.addClass("is-invalid");
    } else if (instantPriceLower) {
        // erreur cadre rouge
        errInstant.html("<ul><li>Instant purchase price must be higher than starting bid price.</li></ul>").addClass("text-danger");
        instantOptional.addClass("is-invalid");
    } else {
        // si pas d'erreur, alors valide
        // success : cadre vert
        instantOptional.addClass("is-valid");
    }

    checkFormValidity();
}

async function checkDirectValid() {
    // we keep it neutral if empty, but it's still invalid
    if (direct.val().trim() === "") {
        direct.removeClass("is-valid is-invalid").addClass("border-secondary");
        errDirect.html("").removeClass("text-danger");
        checkFormValidity();
        return;
    }

    // on récupère les données puis on normalize
    const sanitizedBid = startingBid.val().trim() !== "" ? parseFloat(startingBid.val().trim()) : null;
    const sanitizedInstant = instantOptional.val().trim() !== "" ? parseFloat(instantOptional.val().trim()) : null;
    const sanitizedDirect = parseFloat(direct.val().trim());

    // on vérifie si les valeurs ont bien été normalizées
    const startingBidExists = sanitizedBid !== null;
    const instantOptionalExists = sanitizedInstant !== null;
    const directExists = true; // vrai par défaut puisqu'on a passé la première condition

    // erreur si start bid ou prix optionnel a déjà été entré
    const bothExists = directExists && (startingBidExists || instantOptionalExists);

    // erreur si valeur prix direct est inférieure à 1
    const minimumRequired = directExists && sanitizedDirect < 1;

    // on clear l'état du champ
    direct.removeClass("is-invalid is-valid border-secondary");
    errDirect.html("").removeClass("text-danger");

    if (bothExists) {
        // erreur : cadre rouge
        errDirect.html("<ul><li>You can't put a direct sale price with starting bid or optional instant price.</li></ul>").addClass("text-danger");
        direct.addClass("is-invalid");
    } else if (minimumRequired) {
        // erreur cadre rouge
        errDirect.html("<ul><li>Minimum value 1.</li></ul>").addClass("text-danger");
        direct.addClass("is-invalid");
    } else {
        // si pas d'erreur, alors valide
        // success : cadre vert
        errDirect.html("").removeClass("text-danger");
        direct.addClass("is-valid");
    }

    checkFormValidity();
}

function checkFormValidity() {
    // 1. État de validité des champs individuels
    const isTitleValid = itemTitle.hasClass("is-valid");
    const isDescriptionAcceptable = !description.hasClass("is-invalid");
    const isDurationValid = duration.hasClass("is-valid");

    // 2. États spécifiques aux prix et enchères
    const isStartingBidValid = startingBid.hasClass("is-valid");
    const isDirectPriceValid = direct.hasClass("is-valid");
    const hasStartingBidError = startingBid.hasClass("is-invalid");
    const hasDirectPriceError = direct.hasClass("is-invalid");

    // 3. Validation des modes de vente (Logique métier)
    // Mode Enchère : Mise à prix valide ET aucune erreur sur le prix direct
    const isAuctionModeValid = isStartingBidValid && !hasDirectPriceError;

    // Mode Vente Directe : Aucune erreur sur la mise à prix ET prix direct valide
    const isDirectSaleModeValid = !hasStartingBidError && isDirectPriceValid;

    // 4. Validation finale du formulaire
    const isFormValid = isTitleValid && isDescriptionAcceptable && isDurationValid && (isAuctionModeValid || isDirectSaleModeValid);

    if (!isFormValid) {
        submitBtn.prop("disabled", true);
    } else {
        submitBtn.prop("disabled", false);
    }
}

function handleLeaveConfirmation(e) {
    if (hasUnsavedChanges()) {
        // on arrête la navigation
        e.preventDefault();

        // on affiche la box de confirmation
        const redirect = e.currentTarget.href; // lien de redirection normal si l'utilisateur choisit de quitter
        askConfirm(redirect);
    }

    // si pas de de changement, le bouton agit normalement
}

function hasUnsavedChanges() {
    // on recup les champs
    const fields = Array.from($("#title, #description, #duration, #starting_bid, #instant, #direct"));

    // on check si il y a eu des changements depuis le chargement de la page
    const hasChanges = fields.some((field) => {
        return field.value !== field.defaultValue;
    });
    return hasChanges;
}

function askConfirm(redirect) {
    // affiche la box de confirmation
    $("#unsavedModal").modal("show");

    // redirection
    const leaveBtn = $("#confirmLeaveBtn");

    // .off pour empêcher le stack de plusieurs évènements 'quitter' à l'appui du bouton
    leaveBtn.off("click").on("click", (e) => {
        // prend la fenêtre courante du navigateur et lui assigne l'url de destination
        window.location.href = redirect;
    });
}
