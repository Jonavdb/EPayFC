import { fetchConstants } from './get_constants.js';

let fullName, pseudo, email, iban, errFullName, errPseudo, errEmail, errIban, submitBtn;
const APP_CONFIG = await fetchConstants();

$(function () {
    submitBtn = $('#submitBtn');
    submitBtn.prop('disabled', true); // on force le disabled au démarrage, c'est JS qui l'activera

    pseudo = $('#pseudo');
    errPseudo = $('#errPseudo');
    fullName = $('#full_name');
    errFullName = $('#errFullName');
    email = $('#email');
    errEmail = $('#errEmail');
    iban = $('#iban');
    errIban = $('#errIban');

    pseudo.bind('blur', checkPseudoExists);
    fullName.bind('blur', checkFullNameExists);
    email.bind('blur', checkEmailExists);
    iban.bind('blur', checkIbanFormat);
});

async function checkFullNameExists() {
    const val = fullName.val().trim();
    const minLen = parseInt(APP_CONFIG.full_name_min_length) || 3;

    // validation longueur
    if (val === '' || val.length < minLen) {
        errFullName.html(`Le nom complet doit comporter au moins ${minLen} caractères.`).addClass('text-danger small mt-1');
        fullName.removeClass('is-valid border-secondary').addClass('is-invalid');
        checkFormValidity();
        return;
    }

    // validation serveur
    try {
        const data = await $.getJSON(`user/full_name_exists_service`, { full_name: fullName.val().trim() });

        if (data.exists === true) {
            // erreur
            errFullName.html(`Le full name ${fullName.val()} existe déjà !`).addClass('text-danger small mt-1');
            fullName.removeClass('is-valid border-secondary').addClass('is-invalid');
        } else {
            // succès
            errFullName.html('').removeClass('text-danger');
            fullName.removeClass('is-invalid border-secondary').addClass('is-valid');
        }

        checkFormValidity();
    } catch (error) {
        console.error('Erreur AJAX:', error);
    }
}

async function checkPseudoExists() {
    const val = pseudo.val().trim();
    const minLen = parseInt(APP_CONFIG.pseudo_min_length) || 3;
    const maxLen = parseInt(APP_CONFIG.pseudo_max_length) || 16;

    // validation longueur
    if (val === '' || val.length < minLen || val.length > maxLen) {
        errPseudo.html(`Le pseudo doit faire entre ${minLen} et ${maxLen} caractères.`).addClass('text-danger small mt-1');
        pseudo.removeClass('is-valid border-secondary').addClass('is-invalid');
        checkFormValidity();
        return;
    }

    // validation serveur
    try {
        const data = await $.getJSON(`user/pseudo_exists_service`, { pseudo: val });

        if (data.exists === true) {
            errPseudo.html(`Le pseudo ${val} est déjà pris !`).addClass('text-danger small mt-1');
            pseudo.removeClass('is-valid border-secondary').addClass('is-invalid');
        } else {
            errPseudo.html('').removeClass('text-danger');
            pseudo.removeClass('is-invalid border-secondary').addClass('is-valid'); // ✅ Corrigé
        }

        checkFormValidity();
    } catch (error) {
        console.error('Erreur AJAX:', error);
    }
}

async function checkEmailExists() {
    const val = email.val().trim();
    let regexStr = APP_CONFIG.email_regex;

    // nettoyage obligatoire pour que JS comprenne la regex
    if (regexStr && regexStr.startsWith('/') && regexStr.endsWith('/')) {
        regexStr = regexStr.slice(1, -1);
    }

    const emailRegex = new RegExp(regexStr);

    // validation email valide + regex qui force le  @
    if (val === '' || !emailRegex.test(val)) {
        errEmail.html(`Veuillez entrer une adresse email valide.`).addClass('text-danger small mt-1');
        email.removeClass('is-valid border-secondary').addClass('is-invalid');
        checkFormValidity();
        return;
    }

    // validation serveur
    try {
        const data = await $.getJSON(`user/email_exists_service`, { email: val });

        if (data.exists === true) {
            errEmail.html(`L'email ${val} est déjà utilisé !`).addClass('text-danger small mt-1');
            email.removeClass('is-valid border-secondary').addClass('is-invalid');
        } else {
            errEmail.html('').removeClass('text-danger');
            email.removeClass('is-invalid border-secondary').addClass('is-valid');
        }

        checkFormValidity();
    } catch (error) {
        console.error('Erreur AJAX:', error);
    }
}

// fonction synchrone pour le iban
function checkIbanFormat() {
    const val = iban.val().trim();
    let regexStr = APP_CONFIG.iban_regex;

    // si c'est vide, c'est valide (iban optionnel dans les consignes)
    if (val === '') {
        iban.removeClass('is-invalid is-valid').addClass('border-secondary');
        errIban.html('');
        checkFormValidity();
        return;
    }

    // nettoyage obligatoire pour que JS comprenne la regex
    if (regexStr && regexStr.startsWith('/') && regexStr.endsWith('/')) {
        regexStr = regexStr.slice(1, -1);
    }

    const ibanRegex = new RegExp(regexStr, 'i');

    if (val === '' || !ibanRegex.test(val)) {
        const ibanFormat = APP_CONFIG.iban_format || 'BE99 9999 9999 9999';

        // erreur
        errIban.html(`Le format de l'IBAN doit être : ${ibanFormat}`).addClass('text-danger small mt-1');
        iban.removeClass('is-valid border-secondary').addClass('is-invalid');
    } else {
        // succès
        errIban.html('').removeClass('text-danger');
        iban.removeClass('is-invalid border-secondary').addClass('is-valid');
    }

    checkFormValidity();
}

// désactive le bouton si une au moins une class "is-invalid" est trouvée
function checkFormValidity() {
    if ($('.is-invalid').length > 0) {
        submitBtn.prop('disabled', true);
    } else {
        submitBtn.prop('disabled', false);
    }
}
