import { fetchConstants } from "./get_constants.js";

let signupBtn, email, errEmail, fullName, errFullName, pseudo, errPseudo, password, errPassword, passwordConfirm, errPasswordConfirm;

const APP_CONFIG = await fetchConstants();

$(function () {
    signupBtn = $("#btnSignup");

    email = $("#email");
    errEmail = $("#errEmail");

    fullName = $("#full_name");
    errFullName = $("#errFullName");

    pseudo = $("#pseudo");
    errPseudo = $("#errPseudo");

    // bindings
    email.bind("blur", checkEmailValid);
    fullName.bind("blur", checkFullNameValid);
    pseudo.bind("blur", checkPseudoValid);
});

async function checkEmailValid() {
    const emailSanitized = email.val().trim();
    if (emailSanitized === "") {
        // champ laissé vide : affichage texte par défaut et on masque l'erreur
        errEmail.html("").removeClass("text-danger");
        email.removeClass("is-invalid is-valid").addClass("border-secondary");
        checkFormValidity();
        return;
    }

    // erreur : email existe déjà
    let alreadyExists = false;
    try {
        const data = await $.getJSON("user/email_exists_service", { email: emailSanitized });
        alreadyExists = data.exists === true;
    } catch (error) {
        console.error("Erreur AJAX:", error);
    }

    // erreur : format invalide
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const isInvalidFormat = emailRegex.test(emailSanitized) === false;

    // on clear l'état du champ
    email.removeClass("is-valid is-invalid border-secondary");
    errEmail.html("").removeClass("text-danger");

    if (alreadyExists) {
        // erreur : cadre rouge
        errEmail.html("<ul><li>Cette adresse email est déjà utilisée.</li></ul>").addClass("text-danger");
        email.addClass("is-invalid");
    } else if (isInvalidFormat) {
        // erreur cadre rouge
        errEmail.html("<ul><li>Le format de l'adresse email n'est pas valide.</li></ul>").addClass("text-danger");
        email.addClass("is-invalid");
    } else {
        // si pas d'erreur, alors valide
        // success : cadre vert
        email.addClass("is-valid");
    }
    checkFormValidity();
}

async function checkFullNameValid() {
    const fullNameSanitized = fullName.val().trim();
    const length = fullNameSanitized.length;
    const minSize = APP_CONFIG.full_name_min_length;
    if (fullNameSanitized === "") {
        // champ laissé vide : affichage texte par défaut et on masque l'erreur
        errFullName.html("").removeClass("text-danger");
        fullName.removeClass("is-invalid is-valid").addClass("border-secondary");
        checkFormValidity();
        return;
    }

    // erreur : full name existe déjà
    let alreadyExists = false;
    try {
        const data = await $.getJSON(`user/full_name_exists_service`, { full_name: fullNameSanitized });
        alreadyExists = data.exists === true;
    } catch (error) {
        console.error("Erreur AJAX:", error);
    }

    // erreur : format invalide
    const tooShort = length < minSize;

    // on clear l'état du champ
    fullName.removeClass("is-valid is-invalid border-secondary");
    errFullName.html("").removeClass("text-danger");

    if (alreadyExists) {
        // erreur : cadre rouge
        errFullName.html("<ul><li>Ce nom est déjà utilisé.</li></ul>").addClass("text-danger");
        fullName.addClass("is-invalid");
    } else if (tooShort) {
        // erreur cadre rouge
        errFullName.html("<ul><li>Le nom complet doit comporter au moins " + minSize + " caractères.</li></ul>").addClass("text-danger");
        fullName.addClass("is-invalid");
    } else {
        // si pas d'erreur, alors valide
        // success : cadre vert
        fullName.addClass("is-valid");
    }
    checkFormValidity();
}

async function checkPseudoValid() {
    const pseudoSanitized = pseudo.val().trim();
    const length = pseudoSanitized.length;
    const minSize = APP_CONFIG.pseudo_min_length;
    const maxSize = APP_CONFIG.pseudo_max_length;
    if (pseudoSanitized === "") {
        // champ laissé vide : affichage texte par défaut et on masque l'erreur
        errPseudo.html("").removeClass("text-danger");
        pseudo.removeClass("is-invalid is-valid").addClass("border-secondary");
        checkFormValidity();
        return;
    }

    // erreur : pseudo name existe déjà
    let alreadyExists = false;
    try {
        const data = await $.getJSON("user/pseudo_exists_service", { pseudo: pseudoSanitized });
        alreadyExists = data.exists === true;
    } catch (error) {
        console.error("Erreur AJAX:", error);
    }

    // erreur : longueur invalide
    const isInvalidLength = length < minSize || length > maxSize;

    // erreur : format invalid
    const rawRegex = APP_CONFIG.pseudo_regex.replace(/^\/|\/$/g, "");
    const pseudoRegex = new RegExp(rawRegex);
    const isInvalidFormat = !pseudoRegex.test(pseudoSanitized);

    // on clear l'état du champ
    pseudo.removeClass("is-valid is-invalid border-secondary");
    errPseudo.html("").removeClass("text-danger");

    if (alreadyExists) {
        // erreur : cadre rouge
        errPseudo.html("<ul><li>Ce pseudo est déjà utilisé.</li></ul>").addClass("text-danger");
        pseudo.addClass("is-invalid");
    } else if (isInvalidLength) {
        // erreur cadre rouge
        errPseudo
            .html("<ul><li>Le pseudo doit avoir une longueur comprise entre " + minSize + " et " + maxSize + " caractère</li></ul>")
            .addClass("text-danger");
        pseudo.addClass("is-invalid");
    } else if (isInvalidFormat) {
        // erreur cadre rouge
        errPseudo.html("<ul><li>Le pseudo doit commencer par une lettre et ne contenir que des caractères alphanumériques.</li></ul>").addClass("text-danger");
        pseudo.addClass("is-invalid");
    } else {
        // si pas d'erreur, alors valide
        // success : cadre vert
        pseudo.addClass("is-valid");
    }
    checkFormValidity();
}
function checkFormValidity() {
    // 1. validation email
    const isEmailValid = email.hasClass("is-valid");

    // 2. validation full name
    const isFullNameValid = fullName.hasClass("is-valid");

    // 3. validation pseudo
    const isPseudoValid = pseudo.hasClass("is-valid");

    // ?. validation finale du formulaire
    const isFormValid = isEmailValid && isFullNameValid && isPseudoValid;

    if (!isFormValid) {
        signupBtn.prop("disabled", true);
    } else {
        signupBtn.prop("disabled", false);
    }
}
