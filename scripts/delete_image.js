let cardToDelete = null; // variable globale pour stocker la carte à supprimer
let filenameToDelete = null; // variable globale pour stocker le nom du fichier à supprimer
$(document).ready(function () {
    const item_id = $('#sortable-images').data('item-id');
    $(document).on('click', '.btn_delete', function (e) {
        // on gère aussi les boutons de suppression pour afficher une confirmation avant de supprimer l'image
        e.preventDefault();
        cardToDelete = $(this).closest('.card'); // on stocke la carte à supprimer dans la variable globale
        filenameToDelete = $(this).closest('form').data('image-url'); // on stocke le nom du fichier à supprimer dans la variable globale
        var maModal = new bootstrap.Modal(document.getElementById('modal_confirmation_delete'));
        maModal.show();
    });

    $(document).on('click', '.btn_confirm_delete', async function (e) {
        await $.post('item/delete_images_service', {
            item_id: item_id,
            image_filename: filenameToDelete,
        }); // on ne supprime la carte qu'après le callback du Post
        cardToDelete.remove(); // on supprime la carte de l'interface après la suppression sur le serveur
        bootstrap.Modal.getInstance(document.getElementById('modal_confirmation_delete')).hide(); // on cache la modal de confirmation
        update_buttons(); // on met à jour l'état des boutons (enabled/disabled) après la suppression
    });
    function update_buttons() {
        const cards = $('#sortable-images .card');
        cards.find('.btn-left, .btn-right').prop('disabled', false);
        cards.first().find('.btn-left').prop('disabled', true);
        cards.last().find('.btn-right').prop('disabled', true);
    }
});
