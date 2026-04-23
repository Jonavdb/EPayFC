let fullName, pseudo, email, iban, errFullName, errPseudo, errEmail, errIban, submitBtn;

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
    if (fullName.val().trim() === '') return;

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
    if (pseudo.val().trim() === '') return;

    try {
        const data = await $.getJSON(`user/pseudo_exists_service`, { pseudo: pseudo.val().trim() });

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
        const data = await $.getJSON(`user/email_exists_service`, { email: email.val().trim() });

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

// fonction synchrone pour le iban
function checkIbanFormat() {
    const ibanValue = iban.val().trim();

    // si c'est vide, c'est valide (iban optionnel dans les consignes)
    if (ibanValue === '') {
        iban.removeClass('is-invalid is-valid').addClass('border-secondary');
        errIban.html('');
        checkFormValidity();
        return;
    }

    const ibanRegex = /^[A-Z]{2}\d{2}[A-Z0-9]{1,30}$/i;

    if (!ibanRegex.test(ibanValue)) {
        // erreur
        errIban.html("Le format de l'IBAN est invalide.").addClass('text-danger small mt-1');
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
