// Edit profile : validations synchrones et asynchrones sur les champs
// full name, pseudo, email et IBAN (disponibilité email / pseudo / full name via AJAX en excluant l'utilisateur courant ;
// obligatoire, longueurs, format email, format IBAN optionnel en synchrone).
// En PHP (PRG), les erreurs sont affichées sous chaque champ concerné (pas de bloc d'erreurs global).

let fullName, pseudo, email, iban, errFullName, errPseudo, errEmail, errIban, submitBtn;

$(function () {
    submitBtn = $('#submitBtn');

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
});

async function checkFullNameExists() {
    if (fullName.val().trim() === '') return;

    try {
        const data = await $.getJSON(`user/full_name_exists_service/${fullName.val()}`);

        if (data) {
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
    if (pseudo.val().trim() === '') return;

    try {
        const data = await $.getJSON(`user/pseudo_exists_service`, { pseudo: pseudo.val().trim() });

        console.log(data);

        if (data.exists === true) {
            // erreur
            errPseudo.html(`Le pseudo ${pseudo.val()} existe déjà !`).addClass('text-danger small mt-1');
            pseudo.removeClass('is-valid border-secondary').addClass('is-invalid');
        } else {
            // succès
            errPseudo.html('').removeClass('text-danger');
            pseudo.removeClass('is-invalid border-secondary').addClass('is-valid');
        }

        checkFormValidity();
    } catch (error) {
        console.error('Erreur AJAX:', error);
    }
}

async function checkEmailExists() {
    if (email.val().trim() === '') return;

    try {
        const data = await $.post(`user/email_exists_service`, { email: email.val().trim() }, 'json');

        if (data.exists === true) {
            // erreur : cadre rouge
            errEmail.html(`L'email ${email.val()} existe déjà !`).addClass('text-danger small mt-1');
            email.removeClass('is-valid border-secondary').addClass('is-invalid');
        } else {
            // succès : cadre vert
            errEmail.html('').removeClass('text-danger');
            email.removeClass('is-invalid border-secondary').addClass('is-valid');
        }

        checkFormValidity();
    } catch (error) {
        console.error('Erreur AJAX:', error);
    }
}

// désactive le bouton si une au moins une class "is-invalid" est trouvée
function checkFormValidity() {
    if ($('.is-invalid').length > 0) {
        submitBtn.prop('disabled', true);
    } else {
        submitBtn.prop('disabled', false);
    }
}
