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
    }

    public function show_details(): void
    {

        if (isset($_GET['param1'])) {
            $id = (int)$_GET['param1'];
        } else {
            $id = 3;
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

    public function manage_images(): void
    {

        if (isset($_GET['param1'])) {
            $id = (int)$_GET['param1'];
        } else {
            $this->redirect("BrowseItems", "show_items");
            return;
        }

        $item = Item::get_item_by_id($id);

        $current_user = "Boris";

        if (!$item) {
            $this->redirect("BrowseItems", "show_items");
            return;
        }

        if ($item->owner_name !== $current_user) {
            $this->redirect("ViewOpenItem", "show_details", $id);
            return;
        }

        (new View("manage_images"))->show([
            "item" => $item
        ]);
    }

    public function move_image_left()
    {
        $image_index = isset($_POST['image_index']) ? (int)$_POST['image_index'] : 0;
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        if ($item_id > 0) {
            Item::change_pics_left($image_index, $item_id);
        }
        $this->redirect("ViewOpenItem", "manage_images", $item_id);
    }

    public function move_image_right()
    {
        $image_index = isset($_POST['image_index']) ? (int)$_POST['image_index'] : 0;
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        if ($item_id > 0) {
            Item::change_pics_right($image_index, $item_id);
        }
        $this->redirect("ViewOpenItem", "manage_images", $item_id);
    }

    public function delete_images()
    {
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $filename = isset($_POST['image_filename']) ? $_POST['image_filename'] : '';

        if ($item_id > 0 && !empty($filename)) {
            Item::delete_images($filename, $item_id);
        }
        $this->redirect("ViewOpenItem", "manage_images", $item_id);
    }

    public function new_bid()
    {

        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0.0;

        $current_user_pseudo = "Boris";

        if ($item_id > 0 && $amount > 0) {
            Bid::create_bid($current_user_pseudo, $item_id, $amount);
        }

        $this->redirect("view_open_item", "show_details", $item_id);
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
        $this->redirect("view_open_item", "show_details", $item_id);
    }

    public function edit()
    {
        $item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;
        (new View("edit_item"))->show([
            "id" => $item_id
        ]);
    }
}
