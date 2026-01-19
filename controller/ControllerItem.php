<?php

require_once "framework/Controller.php";
require_once "model/User.php";
require_once "model/Item.php";
require_once "model/Bid.php";
require_once "utils/AppTime.php";

class ControllerItem extends Controller {
    public function index(): void {
        $this->redirect("item", "list");
    }

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

    /* Prépare les données pour l'affichage */
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

     public function open(): void
    {
        // 2. On regarde si le premier argument (l'ID) existe
        if (isset($_GET['param1'])) {
            $id = (int)$_GET['param1']; // On récupère le 8 de l'URL ".../index/8"
        } else {
            $id = 3; // Valeur par défaut si l'URL est juste ".../index"
        }
        $item = Item::get_item_by_id($id);

        $bids = Bid::get_bid_by_item_id($id);
        $current_user_pseudo = 'Boris';

        (new View("open_item"))->show([
            "item" => $item,
            "bids" => $bids,
            "current_user" => $current_user_pseudo
        ]);
    }

    public function new_bid()
    {

        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0.0;

        $current_user_pseudo = "Boris";

        if ($item_id > 0 && $amount > 0) {
            Bid::create_bid($current_user_pseudo, $item_id, $amount);
        }

        $this->redirect("item", "open", $item_id);
    }

    public function buy_now()
    {
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $current_user_pseudo = "Boris";
        if ($item_id > 0) {

            $item = Item::get_item_by_id($item_id);

            if ($item && $item->has_buy_now && !$item->buy_now_reached) {
                Bid::create_bid($current_user_pseudo, $item_id, $item->buy_now_price);
            }
        }
        $this->redirect("item", "open", $item_id);
    }

    public function edit()
    {
        $item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;
        (new View("edit_item"))->show([
            "id" => $item_id
        ]);
    }




}
