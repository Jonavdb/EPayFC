let itemTitle, description, duration, submitBtn;

$(function () {
  submitBtn = $("#submitBtn");

  itemTitle = $("#title");
  errTitle = $("#errTitle");

  description = $("#description");
  errDescription = $("#errDescription");

  duration = $("#duration");
  errDuration = $("#errDuration");

  itemTitle.bind("blur", checkTitleValid);
});

async function checkTitleValid() {
  if (itemTitle.val().trim() === "") {
    // champ laissé vide : affichage texte par défaut et on masque l'erreur
    errTitle.html("").removeClass("text-danger");
    itemTitle
      .removeClass("is-invalid is-valid border-primary")
      .addClass("border-secondary");
    checkFormValidity();
    return;
  }

  try {
    const data = await $.getJSON(
      "item/title_valid_service",
      { title: itemTitle.val().trim() },
      "json",
    );

    if (data.is_valid === true) {
      // succès : cadre vert
      errTitle.html("").removeClass("text-danger");
      itemTitle.removeClass("is-invalid border-secondary").addClass("is-valid");
    } else {
      // erreur : cadre rouge
      errTitle
        .html("<ul><li>Title length must be between 2 and 255.</li></ul>")
        .addClass("text-danger small mt-1");
      itemTitle.removeClass("is-valid border-secondary").addClass("is-invalid");
    }

    checkFormValidity();
  } catch (error) {
    console.error("Erreur AJAX:", error);
  }
}

function checkFormValidity() {
  if ($(".is-invalid").length > 0) {
    submitBtn.prop("disabled", true);
  } else {
    submitBtn.prop("disabled", false);
  }
}
