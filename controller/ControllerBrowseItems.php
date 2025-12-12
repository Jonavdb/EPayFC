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


        // static values to test
        /*
            $title = "title";
            $owner_name = "owner name";
            $main_image_url = "uploads/items/1/25cdaf4224c6841a_1762786661_thumbnail.jpg";
            $image_urls = ["uploads/items/1/25cdaf4224c6841a_1762786661_thumbnail.jpg", "uploads/items/1/25cdaf4224c6841a_1762786661_thumbnail.jpg"];
            $image_count = count($image_urls) - 1;
            $price = 100.14;
            $currentBid = 88.10;
            $created_at = new DateTime("2025-1-12 14:30:00");
            $duration_days = 7;
            $has_bidder = true;
            $is_in_auction = true;
            $item = new ItemM(
                $title,
                $owner_name,
                $main_image_url,
                $image_urls,
                $image_count,
                $price,
                $currentBid,
                $created_at,
                $duration_days,
                $has_bidder,
                $is_in_auction
            );
            */

        // test debug
        $user_bid_items = ItemM::get_user_bid_items('Boris');

        /* 
            query to get all items in auction where user has not place a bid 
            SELECT vis.id from v_items_status vis 
            WHERE TIMESTAMP('2025-12-10 15:30:00') => placeholder current date
                BETWEEN '2025-12-01 00:00:00' AND '2025-12-31 23:59:59'; => placeholders for created_at and end_at of items for 
                AND vis.is_auction = 1
                AND vis.not_purchased_direct_sale = 1
                AND vis.id NOT IN (SELECT distinct 
                                    i.id as id
                                    from users u 
                                    join bids b on b.owner = u.id
                                    join items i on i.id = b.item
                                    where u.pseudo = 'Boris')
                order by vis.id;
        */

        // TODO -> query to get all items not in auction and not purchased and owner is not user himself and date is valid
        /*      SELECT vis.id from v_items_status vis
                WHERE  BETWEEN '2025-12-01 00:00:00' AND '2025-12-31 23:59:59'; => placeholders for created_at and end_at of items for
                AND vis.is_auction = 0
                AND vis.not_purchased_direct_sale = 1
                AND vis.owner NOT (SELECT distinct 
               	u.id
                from users u 
                where u.pseudo = 'Boris')
                ORDER by vis.id;
        */
        // $other_available_items = [];

        (new View("browse_items"))->show([
            "user_bid_items" => $user_bid_items
        ]);
    }
}
