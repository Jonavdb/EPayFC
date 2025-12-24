<?php

require_once "framework/Controller.php";
require_once "model/User.php";
require_once "model/Item.php";

class ControllerItem extends Controller {
    public function index(): void {
        $this->redirect("item", "list");
    }

    public function list(): void {
        $user = $this->get_user_or_redirect();

        // Récupère toutes les annonces créées par l'utilisateur connecté
        $items = Item::get_items_by_owner($user->id);

        $active_items = [];
        $sold_items = [];
        $unsold_items = [];

        $now = new DateTime(); // Date actuelle pour comparer

        foreach ($items as $item) {
            $finish_date = $item->get_finish_date();

            // Si la date de fin est dans le futur => ACTIVE
            if ($finish_date > $now) {
                $active_items[] = $item;
            } else {    // Sinon : CLÔTURÉE (Sold ou Unsold)
                if ($item->is_sold()) {
                    $sold_items[] = $item;
                } else {
                    $unsold_items[] = $item;
                }
            }
        }

        (new View("my_items"))->show([
            "user" => $user,
            "active_items" => $active_items,
            "sold_items" => $sold_items,
            "unsold_items" => $unsold_items
        ]);
    }
}
