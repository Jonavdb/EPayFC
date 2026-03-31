let itemTitle, description, duration, submitBtn;

$(function () {


    submitBtn = $("#submitBtn");

    itemTitle = $("#title");
    errTitle = $("#errTitle"); 

    description = $("#description");
    errDescription = $("#errDescription");

    duration = $("#duration");
    errDuration = $("errDuration");

    itemTitle.bind("blur", checkTitleValid);
});

async function checkTitleValid() {
  if (itemTitle.val().trim() === "") return;

  try {
    const data = await $.post('item/title_valid_service', {title: itemTitle.val.trim() }, 'json');

    if(data.is_valid === true) {
        // erreur : cadre rouge
        // TODO : Finish
        errTitle.html('Title length must be between 2 and 255').addClass('text-danger small mt-1');
        itemTitle.removeClass('')
    }
  }
}
