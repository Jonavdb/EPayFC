controller
<?php
require_once "framework/Controller.php";
require_once "model/ItemM.php";
require_once "model/User.php";

class ControllerBrowseItems extends Controller
{

    public function index(): void
    {
        $this->show_items();
    }

    public function show_items(): void
    {
        $user_bid_items = ItemM::get_user_bid_items('Marc');
        $user_available_items = ItemM::get_user_available_items('Marc');

        (new View("browse_items"))->show([
            "user_bid_items" => $user_bid_items,
            "user_available_items" => $user_available_items
        ]);
    }
}
