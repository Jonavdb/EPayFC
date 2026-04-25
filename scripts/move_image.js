$(document).ready(function () {
    const item_id = $('#sortable-images').data('item-id');
    $('#sortable-images').sortable({
        // on rend les images sortables avec jQueryUI
        tolerance: 'pointer', // déplacement plus précis
        stop: async function () {
            // quand l'image a été lachée, on déclenche la fonction
            const ordre = $('#sortable-images').sortable('toArray', {
                attribute: 'data-image-url',
            });

            await $.post('item/update_image_order', {
                // envoie une requête POST sur le controlleur item et la méthode update_image_order en lui passant 2 paramètres
                item_id: item_id,
                order: ordre,
            });
            update_buttons(); // on met à jour l'état des boutons (enabled/disabled) après le déplacement
        },
    });

    $('.btn-left').on('click', async function (e) {
        // on gère aussi les boutons de déplacement à gauche et à droite pour les utilisateurs qui ne voudraient pas faire du drag and drop
        e.preventDefault();
        const card = $(this).closest('.card'); // closest permet de trouver la carte qui se rapporte à ce bouton
        const previousCard = card.prev('.card'); // prev permet de trouver la carte précédente
        if (previousCard.length) {
            const imageIndex = card.data('image-index');

            await $.post('item/move_image_left_service', {
                item_id: item_id,
                image_index: imageIndex,
            });
            previousCard.before(card); // on déplace la carte
            update_buttons(); // on met à jour l'état des boutons (enabled/disabled) après le déplacement
        }
    });

    $('.btn-right').on('click', async function (e) {
        // on gère aussi les boutons de déplacement à gauche et à droite pour les utilisateurs qui ne voudraient pas faire du drag and drop
        e.preventDefault();
        const card = $(this).closest('.card');
        const nextcard = card.next('.card');
        if (nextcard.length) {
            const imageIndex = card.data('image-index');

            await $.post('item/move_image_right_service', {
                item_id: item_id,
                image_index: imageIndex,
            });
            nextcard.after(card); // on déplace la carte
            update_buttons(); // on met à jour l'état des boutons (enabled/disabled) après le déplacement
        }
    });

    function update_buttons() {
        const cards = $('#sortable-images .card');
        cards.find('.btn-left, .btn-right').prop('disabled', false);
        cards.first().find('.btn-left').prop('disabled', true);
        cards.last().find('.btn-right').prop('disabled', true);
    }
});
