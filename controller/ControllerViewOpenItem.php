<?php

require_once "framework/Controller.php";
require_once "utils/AppTime.php";
require_once "model/Item.php";
require_once "model/Bid.php";

class ControllerViewOpenItem extends Controller
{

    public function index(): void
    {
        if (!Configuration::is_dev()) {
            $this->redirect();
        }

        $args = func_get_args();

        // 2. On regarde si le premier argument (l'ID) existe
        if (isset($_GET['param1'])) {
            $id = (int)$_GET['param1']; // On récupère le 8 de l'URL ".../index/8"
        } else {
            $id = 3; // Valeur par défaut si l'URL est juste ".../index"
        }
        $item = Item::get_item_by_id($id);

        $bids = Bid::get_bid_by_item_id($id);

        (new View("open_item"))->show([
            "item" => $item,
            "bids" => $bids
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

        $this->redirect("view_open_item", "index", $item_id);
    }

    public function buy_now()
    {
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $current_user_pseudo = "Boris";
        if ($item_id > 0) {

            // 2. On charge l'article pour vérifier son prix et son statut
            $item = Item::get_item_by_id($item_id);

            // 3. VÉRIFICATIONS DE SÉCURITÉ
            // - L'item existe ?
            // - L'option Buy Now est activée ?
            // - L'item n'est pas DÉJÀ vendu ? (Important pour éviter les doublons)
            if ($item && $item->has_buy_now && !$item->buy_now_reached) {

                // 4. INSERTION DE L'ENCHÈRE GAGNANTE
                // On force le montant à être le prix d'achat immédiat
                Bid::create_bid($current_user_pseudo, $item_id, $item->buy_now_price);
            }
        }
        $this->redirect("view_open_item", "index", $item_id);
    }
}
