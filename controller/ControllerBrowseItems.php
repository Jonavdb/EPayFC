controller
<?php
require_once "framework/Controller.php";
require_once "model/Item.php";
require_once "model/User.php";

class ControllerBrowseItems extends Controller
{

    public function index(): void
    {
        $this->show_items();
    }

    public function show_items(): void
    {
        // TODO -> use this to get the pseudo or the id once login view is linked to browse items view
        $user = $this->get_user_or_false();

        // TODO -> replace 'Boris' with user pseudo OR user id
        $user_bid_items = Item::get_user_bid_items('Boris');
        $user_available_items = Item::get_user_available_items('Boris');

        (new View("browse_items"))->show(
            [
                "user_bid_items" => $user_bid_items,
                "user_available_items" => $user_available_items
            ]
        );
    }
}
