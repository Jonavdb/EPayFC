let itemTitle, description, duration, startingBid, instantOptional, direct, submitBtn;

$(function () {
    submitBtn = $("#submitBtn");

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

    itemTitle.trigger("blur");
    startingBid.trigger("blur");
    description.trigger("blur");
    duration.trigger("blur");
    startingBid.trigger("blur");
    instantOptional.trigger("blur");
    direct.trigger("blur");
});

async function checkTitleValid() {
    if (itemTitle.val().trim() === "") {
        // champ laissé vide : affichage texte par défaut et on masque l'erreur
        errTitle.html("").removeClass("text-danger");
        itemTitle.removeClass("is-invalid is-valid").addClass("border-secondary");
        checkFormValidity();
        return;
    }

    try {
        const data = await $.getJSON("item/title_valid_service", { title: itemTitle.val().trim() }, "json");

        if (data.is_valid === true) {
            // succès :
            hasError = false;
            errTitle.html("").removeClass("text-danger");
            itemTitle.removeClass("is-invalid border-secondary").addClass("is-valid");
        } else {
            // erreur : cadre rouge
            errTitle.html("<ul><li>Title length must be between 2 and 255.</li></ul>").addClass("text-danger small mt-1");
            itemTitle.removeClass("is-valid border-secondary").addClass("is-invalid");
        }

        checkFormValidity();
    } catch (error) {
        console.error("Erreur AJAX:", error);
    }
}

async function checkDescriptionValid() {
    if (description.val().trim() === "") {
        // champ laissé vide : affichage texte par défaut et on masque l'erreur
        errDescription.html("").removeClass("text-danger");
        description.removeClass("is-invalid is-valid").addClass("border-secondary");
        checkFormValidity();
        return;
    }

    try {
        const data = await $.getJSON("item/description_valid_service", { description: description.val().trim() }, "json");

        if (data.is_valid === true) {
            // succès : cadre vert
            errDescription.html("").removeClass("text-danger");
            description.removeClass("is-invalid border-secondary").addClass("is-valid");
        } else {
            // erreur cadre rouge
            errDescription.html("<ul><li>Description length must be between 3 and 65535 characters.</li></ul>").addClass("text-danger small mt-1");
            description.removeClass("is-valid border-secondary").addClass("is-invalid");
        }

        checkFormValidity();
    } catch (error) {
        console.error("Erreur AJAX:", error);
    }
}

async function checkDurationValid() {
    if (duration.val().trim() === "") {
        // champ laissé vide : affichage text par défaut et on masque l'erreur
        errDuration.html("").removeClass("text-danger");
        duration.removeClass("is-invalid is-valid").addClass("border-secondary");
        checkFormValidity();
        return;
    }

    try {
        const durationValue = parseInt(duration.val().trim());
        const data = await $.getJSON("item/duration_valid_service", { duration: durationValue }, "json");

        if (data.is_valid === true) {
            // succès :
            errDuration.html("").removeClass("text-danger");
            duration.removeClass("is-invalid border-secondary").addClass("is-valid");
        } else {
            // erreur : cadre rouge
            errDuration.html("<ul><li>Duration must be at least 1 day.</li></ul>").addClass("text-danger small mt-1");
            duration.removeClass("is-valid").addClass("is-invalid");
        }

        checkFormValidity();
    } catch (error) {
        console.error("Erreur AJAX:", error);
    }
}

async function checkStartingBidValid() {
    if (startingBid.val().trim() === "") {
        // we keep it neutral if empty, but it's still invalid
        startingBid.removeClass("is-invalid is-valid").addClass("border-secondary");
        errStartingBid.html("").removeClass("text-danger");
        checkFormValidity();
        return;
    }

    try {
        const data = await $.getJSON("item/starting_bid_valid_service", {
            starting_bid: startingBid.val(),
            instant: instantOptional.val(),
            direct: direct.val(),
        });

        // we clear the state
        startingBid.removeClass("is-invalid is-valid border-secondary");
        errStartingBid.html("").removeClass("text-danger");

        if (data.both_exist) {
            errStartingBid.html("<ul><li>You can't put a direct sale price and a starting bid.</li></ul>").addClass("text-danger");
            startingBid.addClass("is-invalid");
        } else if (data.minimum_required === true) {
            // erreur cadre rouge
            errStartingBid.html("<ul><li>Starting bid minimum 1.</li></ul>").addClass("text-danger");
            startingBid.addClass("is-invalid");
        } else if (data.instant_price_lower === true) {
            // erreur cadre rouge
            errStartingBid.html("<ul><li>Instant purchase price must be higher than starting bid price.</li></ul>").addClass("text-danger");
            startingBid.addClass("is-invalid");
        } else if (data.default) {
            // erreur cadre rouge
            errStartingBid.html("<ul><li>You must either fill a starting bid or a direct sale price.</li></ul>").addClass("text-danger");
            startingBid.addClass("is-invalid");
        } else {
            // si pas d'erreur, alors valide
            // success : cadre vert
            startingBid.addClass("is-valid");
        }

        checkFormValidity();
    } catch (error) {
        console.error("Erreur AJAX:", error);
    }
}

async function checkOptionalInstantValid() {
    // we keep it neutral if empty, but it's still invalid
    if (instantOptional.val().trim() === "") {
        instantOptional.removeClass("is-invalid is-valid").addClass("border-secondary");
        errInstant.html("").removeClass("text-danger");
        checkFormValidity();
        return;
    }

    try {
        const data = await $.getJSON("item/optional_instant_valid_service", {
            starting_bid: startingBid.val(),
            instant: instantOptional.val(),
            direct: direct.val(),
        });

        // we clear the state
        instantOptional.removeClass("is-invalid is-valid border-secondary");
        errInstant.html("").removeClass("text-danger");

        if (data.neutral) {
            // nothing
            errInstant.html("").removeClass("text-danger");
            instantOptional.removeClass("is-invalid is-valid").addClass("border-secondary");
        } else if (data.both_exist) {
            // erreur cadre rouge
            errInstant.html("<ul><li>You can't put a direct sale price and an optional instant price.</li></ul>").addClass("text-danger");
            instantOptional.addClass("is-invalid");
        } else if (data.no_starting_bid) {
            // erreur cadre rouge
            errInstant.html("<ul><li>You need a starting bid.</li></ul>").addClass("text-danger");
            instantOptional.addClass("is-invalid");
        } else if (data.minimum_required) {
            // erreur cadre rouge
            errInstant.html("<ul><li>Minimum value 1.</li></ul>").addClass("text-danger");
            instantOptional.addClass("is-invalid");
        } else if (data.instant_price_lower) {
            // erreur cadre rouge
            errInstant.html("<ul><li>Instant purchase price must be higher than starting bid price.</li></ul>").addClass("text-danger");
            instantOptional.addClass("is-invalid");
        } else {
            // si pas d'erreur, alors valide
            // success : cadre vert
            instantOptional.addClass("is-valid");
        }

        checkFormValidity();
    } catch (error) {
        console.error("Erreur AJAX:", error);
    }
}

async function checkDirectValid() {
    // we keep it neutral if empty, but it's still invalid
    if (direct.val().trim() === "") {
        direct.removeClass("is-valid is-invalid").addClass("border-secondary");
        errDirect.html("").removeClass("text-danger");
        checkFormValidity();
        return;
    }

    try {
        const data = await $.getJSON("item/direct_sale_valid_service", {
            starting_bid: startingBid.val(),
            instant: instantOptional.val(),
            direct: direct.val(),
        });

        // we clear the state
        direct.removeClass("is-invalid is-valid border-secondary");
        errDirect.html("").removeClass("text-danger");

        if (data.both_exist) {
            // erreur : cadre rouge
            errDirect.html("<ul><li>You can't put a direct sale price with starting bid or optional instant price.</li></ul>").addClass("text-danger");
            direct.addClass("is-invalid");
        } else if (data.minimum_required) {
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
    } catch (error) {
        console.error("Erreur AJAX:", error);
    }
}

function checkFormValidity() {
    const isValidForm =
        itemTitle.hasClass("is-valid") &&
        !description.hasClass("is-invalid") &&
        duration.hasClass("is-valid") &&
        ((startingBid.hasClass("is-valid") && !direct.hasClass("is-invalid")) || (!startingBid.hasClass("is-invalid") && direct.hasClass("is-valid")));

    if (!isValidForm) {
        submitBtn.prop("disabled", true);
    } else {
        submitBtn.prop("disabled", false);
    }
}
