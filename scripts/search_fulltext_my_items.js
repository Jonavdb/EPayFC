let searchInput, activeItemsGrid, closedUnsoldItemsGrid, soldItemsGrid, defaultActiveHtml, defaultClosedUnsoldHtml, defaultSoldHtml;
let initialSearchValue = '';

$(function () {
    $('#search-bar').show();
    searchInput = $('#search_fulltext');
    activeItemsGrid = $('#active_items_grid');
    closedUnsoldItemsGrid = $('#closed_unsold_items_grid');
    soldItemsGrid = $('#sold_items_grid');

    initialSearchValue = searchInput.val().trim(); // mémorise ce qu'il y a dans la barre au chargement

    // page par défaut sauvergardée (les listes d'items de l'utilisateur courrant)
    defaultActiveHtml = activeItemsGrid.html();
    defaultClosedUnsoldHtml = closedUnsoldItemsGrid.html();
    defaultSoldHtml = soldItemsGrid.html();

    searchInput.on('input', checkSearchFullTextMyItems);
    $('#category_filter').on('change', checkSearchFullTextMyItems);
});

async function checkSearchFullTextMyItems() {
    const searchValue = searchInput.val().trim();
    const categoryId = $('#category_filter').val();

    // s'il n'a rien cherché, on affiche les listes par défaut
    if (searchValue === '' && categoryId === '') {
        if (initialSearchValue !== '') {
            window.location.href = 'item/list';
            return;
        }

        // sinon, restauration du cache
        activeItemsGrid.html(defaultActiveHtml);
        closedUnsoldItemsGrid.html(defaultClosedUnsoldHtml);
        soldItemsGrid.html(defaultSoldHtml);
        return;
    }

    try {
        const response = await $.get(`item/search_fulltext_my_items_service`, { search: searchValue, category_id: categoryId }, 'json');

        // vidage des grilles pour y mettre les résultats de manière dynamique
        activeItemsGrid.empty();
        closedUnsoldItemsGrid.empty();
        soldItemsGrid.empty();

        displayMyItems(response.items, response.encoded_search);
    } catch (error) {
        console.error('Erreur AJAX Search My Items:', error);
    }

    // pur affichage : fonction utilitaire qui affiche les items en JS
    function displayMyItems(items, encodedSearch = '') {
        if (items.length > 0) {
            items.forEach((item) => {
                let href = `item/open/${item.id}/from-items`;

                if (encodedSearch !== '') {
                    href += `/${encodedSearch}`;
                }

                // badges, icons, etc
                let contextBadge = '';
                if (item.context === 'sold') {
                    contextBadge = `<span class="badge rounded-pill bg-success"><i class="fa-solid fa-check"></i> Sold</span>`;
                } else if (item.context === 'unsold') {
                    contextBadge = `<span class="badge rounded-pill bg-secondary"><i class="fa-solid fa-ban"></i> Unsold</span>`;
                }

                let auctionBadge = item.is_auction
                    ? `<span class="badge rounded-pill bg-warning text-dark"><i class="fa-solid fa-gavel fa-flip-horizontal"></i> Auction</span>`
                    : '';

                let buyNowBadge =
                    item.has_buy_now && !item.buy_now_reached
                        ? `<span class="badge rounded-pill bg-primary d-inline-flex align-items-center px-3 py-2"><i class="fa-solid fa-bag-shopping me-2"></i> Buy now</span>`
                        : '';

                let extraImagesBadge = '';
                let extraImagesCount = item.extra_images || 0;
                if (extraImagesCount > 0) {
                    let totalImages = extraImagesCount + 1;
                    let s = totalImages > 1 ? 's' : '';
                    extraImagesBadge = `
                        <div class="d-flex justify-content-end">
                            <span class="badge rounded-pill text-bg-dark bg-opacity-75">
                                <i class="fa-solid fa-images"></i> ${totalImages} image${s}
                            </span>
                        </div>`;
                }

                let safePrice = parseFloat(item.price || 0)
                    .toFixed(2)
                    .replace('.', ',');
                let safeStartingBid = parseFloat(item.starting_bid || 0)
                    .toFixed(2)
                    .replace('.', ',');
                let safeCurrentBid = parseFloat(item.current_bid || 0)
                    .toFixed(2)
                    .replace('.', ',');

                let priceHtml = item.has_buy_now
                    ? `<div class="fs-4 fw-bold text-primary">€ ${safePrice}</div>`
                    : `<div class="fs-4 fw-bold text-warning">€ ${safeStartingBid}</div>`;

                let timeColorClass = item.time_left === 'Closed' ? 'text-secondary' : 'text-success';

                let currentBidHtml =
                    item.is_auction && item.context !== 'unsold'
                        ? `
                    <div class="text-end">
                        <div class="text-secondary small fw-bold mb-0">Current bid</div>
                        <div class="fs-4 fw-bold text-success">€ ${safeCurrentBid}</div>
                    </div>`
                        : '';

                let imgSrc = item.main_image_url ? item.main_image_url : 'css/images/default_item_image.png';

                let cardHtml = `
                    <div class="col">
                        <a href="${href}" class="card h-100 text-decoration-none shadow-sm card-hover border-secondary">
                            <div class="ratio ratio-4x3">
                                <img src="${imgSrc}" class="card-img-top" alt="${item.title}" style="object-fit: cover;">

                                <div class="card-img-overlay d-flex flex-column justify-content-between p-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            ${contextBadge}
                                        </div>
                                        <div class="d-flex flex-column align-items-end gap-1">
                                            ${auctionBadge}
                                            ${buyNowBadge}
                                        </div>
                                    </div>

                                    ${extraImagesBadge}
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <div class="text-start card-title text-truncate fw-bold fs-5 mb-0 text-white">${item.title}</div>
                                    <small class="text-secondary">by ${item.owner_name}</small>
                                </div>

                                <div class="d-flex justify-content-between align-items-end mt-auto mb-2 pt-2">
                                    <div class="d-flex flex-column">
                                        ${priceHtml}

                                        <div class="small fw-bold ${timeColorClass} mt-1">
                                            <i class="fa-regular fa-clock"></i> ${item.time_left}
                                        </div>
                                    </div>
                                    ${currentBidHtml}
                                </div>
                            </div>
                        </a>
                    </div>`;

                if (item.context === 'sold') {
                    soldItemsGrid.append(cardHtml);
                } else if (item.context === 'unsold') {
                    closedUnsoldItemsGrid.append(cardHtml);
                } else {
                    activeItemsGrid.append(cardHtml);
                }
            });
        }

        if (activeItemsGrid.children().length === 0) {
            activeItemsGrid.html('<p class="text-secondary mt-3 fs-3 col-12">No item found.</p>');
        }
        if (closedUnsoldItemsGrid.children().length === 0) {
            closedUnsoldItemsGrid.html('<p class="text-secondary mt-3 fs-3 col-12">No item found.</p>');
        }
        if (soldItemsGrid.children().length === 0) {
            soldItemsGrid.html('<p class="text-secondary mt-3 fs-3 col-12">No item found.</p>');
        }
    }
}
