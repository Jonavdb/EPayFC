let searchInput, participatingGrid, availableGrid, defaultParticipatingHtml, defaultAvailableHtml;

$(function () {
    searchInput = $('#search_fulltext');
    participatingGrid = $('#participating_grid');
    availableGrid = $('#available_grid');

    // pages par défaut sauvergardées (les listes d'items de l'utilisateur courrant)
    defaultParticipatingHtml = participatingGrid.html();
    defaultAvailableHtml = availableGrid.html();

    searchInput.on('input', checkSearchFullText);
});

async function checkSearchFullText() {
    const searchValue = searchInput.val().trim();

    // s'il n'a rien cherhcé, on affiche la liste par défaut
    if (searchValue === '') {
        participatingGrid.html(defaultParticipatingHtml);
        availableGrid.html(defaultAvailableHtml);
        return;
    }

    try {
        const items = await $.get(`item/search_fulltext_service`, { search: searchValue }, 'json');

        // vidage des grilles pour y mettre les résultats de manière dynamique
        participatingGrid.empty();
        availableGrid.empty();

        displayItems(items);
    } catch (error) {
        console.error('Erreur AJAX:', error);
    }

    // pur affichage : fonction utilitaire qui affiche les items en JS
    function displayItems(items) {
        if (items.length > 0) {
            items.forEach((item) => {
                // affichage des petites icones, etc
                let auctionBadge = item.is_auction
                    ? `<span class="badge rounded-pill bg-warning text-dark"><i class="fa-solid fa-gavel fa-flip-horizontal"></i> Auction</span>`
                    : '';

                let buyNowBadge =
                    item.has_buy_now && !item.buy_now_reached
                        ? `<span class="badge rounded-pill bg-primary"><i class="fa-solid fa-bag-shopping"></i> Buy now</span>`
                        : '';

                let priceHtml = item.has_buy_now
                    ? `<div class="fs-4 fw-bold text-primary">€ ${parseFloat(item.price).toFixed(2).replace('.', ',')}</div>`
                    : `<div class="fs-4 fw-bold text-warning">€ ${parseFloat(item.starting_bid).toFixed(2).replace('.', ',')}</div>`;

                let currentBidHtml = item.is_auction
                    ? `
                    <div class="text-end">
                        <div class="text-secondary small fw-bold mb-0">Current bid</div>
                        <div class="fs-4 fw-bold text-success">€ ${parseFloat(item.current_bid).toFixed(2).replace('.', ',')}</div>
                    </div>`
                    : '';

                let imgSrc = item.main_image_url ? item.main_image_url : 'css/images/default_item_image.png';

                // la carte complète
                let cardHtml = `
                    <div class="col">
                        <a href="item/open/${item.id}/from-browse" class="card bg-dark h-100 text-decoration-none shadow-sm card-hover border-secondary">
                            <div class="position-relative">
                                <img src="${imgSrc}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="card-img-overlay d-flex flex-column justify-content-between p-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div></div>
                                        <div class="d-flex flex-column align-items-end gap-1">
                                            ${auctionBadge}
                                            ${buyNowBadge}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column text-white">
                                <div class="mb-2">
                                    <div class="text-start card-title text-truncate fw-bold fs-5 mb-0">${item.title}</div>
                                    <small class="text-secondary">by ${item.owner_name}</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-end mt-auto mb-2 pt-2">
                                    <div class="d-flex flex-column">
                                        ${priceHtml}
                                        <div class="small fw-bold text-success mt-1">
                                            <i class="fa-regular fa-clock"></i> ${item.time_left} left
                                        </div>
                                    </div>
                                    ${currentBidHtml}
                                </div>
                            </div>
                        </a>
                    </div>
                `;

                if (item.has_bids) {
                    participatingGrid.append(cardHtml);
                } else {
                    availableGrid.append(cardHtml);
                }

                if (participatingGrid.children().length === 0) {
                    participatingGrid.html('<p class="text-secondary mt-3 fs-3">No item found.</p>');
                }
                if (availableGrid.children().length === 0) {
                    availableGrid.html('<p class="text-secondary mt-3 fs-3">No item found.</p>');
                }
            });
        }
    }
}
