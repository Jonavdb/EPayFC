<?php

require_once "framework/Controller.php";
require_once "model/User.php";
require_once "model/Item.php";

class ControllerItem extends Controller {
    public function index(): void {
        $this->redirect("item", "list");
    }

    // My items
    public function list(): void {
        $user = $this->get_user_or_redirect();
        $items = Item::get_items_by_owner($user->id);

        $active_items = [];
        $sold_items = [];
        $unsold_items = [];
        $now = new DateTime();

        foreach ($items as $item) {
            // 1. Préparation des données d'affichage (photos des items, etc...)
            $display_data = $this->prepare_item_data($item, $now);

            // 2. Tri dans les catégories
            if ($item->end_at > $now) {
                $display_data->context = 'active';
                $active_items[] = $display_data;
            } else {
                $is_sold = ($item->bid_count > 0) || ($item->buy_now_reached);

                if ($is_sold) {
                    $display_data->context = 'sold';
                    $sold_items[] = $display_data;
                } else {
                    $display_data->context = 'unsold';
                    $unsold_items[] = $display_data;
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

    /* Helper pour My Items : Prépare les données pour l'affichage */
    private function prepare_item_data(Item $item, DateTime $now): object {
        $is_closed = $item->end_at <= $now;

        // Calcul du texte temps
        if ($is_closed) {
            $time_left_str = "Closed";
        } else {
            $diff = $now->diff($item->end_at);
            $time_left_str = ($diff->days > 0) ? $diff->days . " days left" : $diff->h . "h " . $diff->i . "m left";
        }

        // Image
        $img_src = !empty($item->main_image_url) ? $item->main_image_url : "uploads/items/item_placeholder.png";

        // Compteur images
        $total_image_count = is_array($item->image_urls) ? count($item->image_urls) : 0;
        $extra_image_count = max(0, $total_image_count - 1);

        // On retourne un objet générique
        return (object) [
            'raw_item' => $item,
            'time_left' => $time_left_str,
            'img_src' => $img_src,
            'extra_images' => $extra_image_count,
            'context' => ''               // Sera rempli dans la boucle list()
        ];
    }

    // delete item
    public function delete($id = null): void {
        if ($id === null && isset($_GET['param1'])) {
            $id = $_GET['param1'];
        }

        if (!$id) {
            $this->redirect("item", "list");
            return;
        }

        $user = $this->get_user_or_redirect();
        $item = Item::get_item_by_id($id);

        // Vérification : L'item existe
        if (!$item) {
            $this->redirect("item", "list");
            return;
        }

        // LOGIQUE DE SUPPRESSION (Si on a cliqué sur "Delete" - POST)
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            Item::delete($id);
            $this->redirect("item", "list");
        } else {
            // LOGIQUE D'AFFICHAGE
            (new View("delete_item"))->show([
                "item" => $item,
                "user" => $user
            ]);
        }
    }
}
