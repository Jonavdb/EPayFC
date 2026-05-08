import { fetchConstants } from './get_constants.js';

const APP_CONFIG = await fetchConstants();

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
    checkedElements,
    footerElements;

$(function () {
    submitBtn = $('#submitBtn');
    goBackBtn = $('#goBack');
    footerElements = $('footer a.nav-link');
    itemTitle = $('#title');
    errTitle = $('#errTitle');
    description = $('#description');
    errDescription = $('#errDescription');
    duration = $('#duration');
    errDuration = $('#errDuration');
    startingBid = $('#starting_bid');
    errStartingBid = $('#errStartingBid');
    instantOptional = $('#instant');
    errInstant = $('#errInstant');
    direct = $('#direct');
    errDirect = $('#errDirect');
    checkedElements = $('[id^="checkboxContainer-"] .form-check-input'); // id récupéré dynamiquement avec cette syntaxe

    checkCheckboxValid();

    // bindings
    itemTitle.bind('blur', checkTitleValid);
    description.bind('blur', checkDescriptionValid);
    duration.bind('blur', checkDurationValid);

    // toute la logique de prix est regroupée sur un seul déclencheur
    $('#starting_bid, #instant, #direct').bind('blur', checkPricingValid);

    // event listeners de navigation
    goBackBtn.on('click', null, (e) => {
        handleLeaveConfirmation(e);
    });

    footerElements.on('click', null, (e) => {
        handleLeaveConfirmation(e);
    });

    // triggers de validation au chargement
    itemTitle.trigger('blur');
    description.trigger('blur');
    duration.trigger('blur');
    startingBid.trigger('blur'); // ce trigger unique validera toute la logique de prix
});

/* FONCTION UTILITAIRE : gère l'affichage visuel des erreurs */
function setFieldState(field, errField, isValid, errMsg = '') {
    field.removeClass('is-valid is-invalid border-secondary');
    errField.removeClass('text-danger').html('');

    if (isValid === true) {
        field.addClass('is-valid');
    } else if (isValid === false) {
        field.addClass('is-invalid');
        errField.html(`<ul><li>${errMsg}</li></ul>`).addClass('text-danger small mt-1');
    } else {
        field.addClass('border-secondary'); // État neutre
    }
}

function checkTitleValid() {
    const val = itemTitle.val().trim();
    if (val === '') return setFieldState(itemTitle, errTitle, null);
    const isValid = val.length >= APP_CONFIG.min_item_length && val.length <= APP_CONFIG.max_item_length;

    setFieldState(itemTitle, errTitle, isValid, 'Title length must be between 2 and 255.');
    checkFormValidity();
}

function checkDescriptionValid() {
    const val = description.val().trim();
    if (val === '') return setFieldState(description, errDescription, null);
    const isValid = val.length >= APP_CONFIG.min_description_length && val.length <= APP_CONFIG.max_description_length;

    setFieldState(description, errDescription, isValid, 'Description length must be between 3 and 65535 characters.');
    checkFormValidity();
}

function checkDurationValid() {
    const val = duration.val().trim();
    if (val === '') return setFieldState(duration, errDuration, null);
    const num = parseInt(val);
    const isValid = num >= APP_CONFIG.min_duration_days && num <= APP_CONFIG.max_duration_days;

    setFieldState(duration, errDuration, isValid, 'Duration must be at least 1 day and at most 365 days.');
    checkFormValidity();
}

function checkCheckboxValid() {
    const MAX_CATEGORIES = 3;

    checkedElements.on('change', function (event) {
        const checkedCount = $('.form-check-input:checked'); // toutes les cochées

        if (checkedCount.length > MAX_CATEGORIES) {
            $(this).prop('checked', false); // dé-check celle de trop
            $('#checkboxModal').modal('show');
        }
    });
}

/* LOGIQUE DE PRIX UNIFIÉE */
function checkPricingValid() {
    const bidVal = startingBid.val().trim();
    const instVal = instantOptional.val().trim();
    const dirVal = direct.val().trim();

    const bid = bidVal !== '' ? parseFloat(bidVal) : null;
    const inst = instVal !== '' ? parseFloat(instVal) : null;
    const dir = dirVal !== '' ? parseFloat(dirVal) : null;

    // on remet tout au neutre visuellement
    setFieldState(startingBid, errStartingBid, null);
    setFieldState(instantOptional, errInstant, null);
    setFieldState(direct, errDirect, null);

    // si TOUT est vide (au chargement ou si l'utilisateur efface tout) on sort de la fonction
    // les champs restent gris/neutres et checkFormValidity bloquera le bouton Submit
    if (dir === null && bid === null && inst === null) {
        return checkFormValidity();
    }

    // erreur : conflit entre Vente Directe et Enchère
    if (dir !== null && (bid !== null || inst !== null)) {
        setFieldState(direct, errDirect, false, "You can't put a direct sale price with starting bid or optional instant price.");

        if (bid !== null) setFieldState(startingBid, errStartingBid, false, "You can't put a direct sale price and a starting bid.");
        if (inst !== null) setFieldState(instantOptional, errInstant, false, "You can't put a direct sale price and an optional instant price.");
        return checkFormValidity();
    }

    // Mode Vente Directe
    if (dir !== null) {
        setFieldState(direct, errDirect, dir >= 1, dir < 1 ? 'Minimum value 1.' : '');
        return checkFormValidity();
    }

    // Mode Enchère (bid ou inst présent)
    let isAuctionValid = true;

    if (bid === null && inst !== null) {
        setFieldState(instantOptional, errInstant, false, 'You need a starting bid.');
        isAuctionValid = false;
    } else if (bid !== null) {
        if (bid <= 0) {
            setFieldState(startingBid, errStartingBid, false, 'Starting bid minimum 1.');
            isAuctionValid = false;
        } else {
            setFieldState(startingBid, errStartingBid, true);
        }
    }

    if (inst !== null) {
        if (inst < 1) {
            setFieldState(instantOptional, errInstant, false, 'Minimum value 1.');
        } else if (bid !== null && inst <= bid) {
            setFieldState(startingBid, errStartingBid, false, 'Instant purchase price must be higher than starting bid price.');
            setFieldState(instantOptional, errInstant, false, 'Instant purchase price must be higher than starting bid price.');
        } else if (isAuctionValid) {
            setFieldState(instantOptional, errInstant, true);
        }
    }

    checkFormValidity();
}

function checkFormValidity() {
    const isTitleValid = itemTitle.hasClass('is-valid');
    const isDescriptionAcceptable = !description.hasClass('is-invalid');
    const isDurationValid = duration.hasClass('is-valid');

    const isAuctionValid = startingBid.hasClass('is-valid') && !direct.hasClass('is-invalid');
    const isDirectSaleValid = direct.hasClass('is-valid') && !startingBid.hasClass('is-invalid');

    const isFormValid = isTitleValid && isDescriptionAcceptable && isDurationValid && (isAuctionValid || isDirectSaleValid);

    submitBtn.prop('disabled', !isFormValid);
}

function handleLeaveConfirmation(e) {
    if (hasUnsavedChanges()) {
        e.preventDefault();
        askConfirm(e.currentTarget.href);
    }
}

function hasUnsavedChanges() {
    const fields = $('#title, #description, #duration, #starting_bid, #instant, #direct').get();
    return fields.some((field) => field.value !== field.defaultValue);
}

function askConfirm(redirectUrl) {
    $('#unsavedModal').modal('show');
    $('#confirmLeaveBtn')
        .off('click')
        .on('click', () => {
            window.location.href = redirectUrl;
        });
}
