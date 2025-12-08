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

        $id = 20;
        $item = Item::get_item_by_id($id);

        $bids = Bid::get_bid_by_item_id($id);

        (new View("open_item"))->show([
            "item" => $item,
            "bids" => $bids
        ]);
    }
}
