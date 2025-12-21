<?php

require_once "framework/Model.php";
require_once "model/Item.php";
require_once "utils/AppTime.php";

class Profile extends Model
{
    public function __construct() {}

    public static function get_purchases($pseudo)
    {
        $purchases = [];

        $query = self::execute(
            // query to get the items where the specified user got the winning 
            // OR the items that he purchased directly
            "SELECT 
            /* Item Data */
            vis.id AS item_id,
            vis.title,
            vis.buy_now_price,
            vis.is_auction,
            vis.has_buy_now,
            vis.buy_now_reached,
            vis.end_at,
            vis.max_bid,

            /* Seller Data (The person who owns the item) */
            seller.id AS seller_id,         -- id of the person who owns the item (seller is the custom name of users table ie check next query lines)
            seller.pseudo AS seller_name,   -- name of the person who owns the item

            /* Bidder Data (The person who placed the winning bid) */
            u.id AS bidder_id,
            u.pseudo AS bidder_name,        -- name of the person of placed the bid 
            b.amount AS winning_bid_amount  -- bid placed by the person 

            FROM v_items_status vis
            /* Join for the bid details */
            JOIN bids b ON b.item = vis.id

            /* Join for the person who placed the bid (Bidder / Buyer) */
            JOIN users u ON u.id = b.owner

            /* Join for the person who created the item (Seller) */
            JOIN users seller ON seller.id = vis.owner 

            WHERE :today > vis.end_at   
            AND u.pseudo = :pseudo          -- we specifiy the target user
            AND b.amount = vis.max_bid      -- we keep only the user the winning bids or what he purchased directly
            ORDER BY vis.id",
            [
                "today" => AppTime::get_current_datetime(),
                "pseudo" => $pseudo
            ]
        );

        $data = $query->fetchAll();
        if (!$data) {
            return false;
        }

        foreach ($data as $item_data) {
            // available items have no user bid on them
            $user_bid = Item::get_user_bid($item_data['item_id'], 4); // for debug, for is boris id

            // we get info of whether the item is in auction or not
            $is_auction = $item_data['is_auction'];

            // we get the info whether or not the item can be purchased directly
            $has_buy_now = $item_data['has_buy_now'];
            $has_buy_now = $has_buy_now == 1 ? true : false;

            // we get the info whether or not the item last bid exceeds the direct sale price
            $buy_now_reached = $item_data['buy_now_reached'];

            // we retrieve item pictures
            $query_image_urls = self::execute(
                "SELECT picture_path
                                FROM item_pictures p
                                where p.item = :item_id
                                Order BY priority ASC",
                ["item_id" => $item_data['item_id']]
            );

            $data_image_urls = $query_image_urls->fetchAll(PDO::FETCH_COLUMN);
            if (!$data_image_urls) {
                return false;
            }

            $image_urls = (array) $data_image_urls;

            // we retrieve main picture
            $main_image_url = $image_urls[0];
            $path_parts = pathinfo($main_image_url);
            $main_image_url = $path_parts['dirname'] . '/' .
                $path_parts['filename'] .
                '_thumbnail.' .
                $path_parts['extension'];

            // we retrieve the title
            $title = $item_data['title'];

            // we retrieve owner name
            $query_owner_name = self::execute(
                "SELECT DISTINCT u.pseudo as owner_name
                                from users u
                                join v_items_status i on i.owner = u.id
                                where i.owner = :owner_id",
                ["owner_id" => $item_data['seller_id']]
            );

            $data_owner_name = $query_owner_name->fetch();

            if (!$data) {
                return false;
            }

            $owner_name = $data_owner_name['owner_name'];

            // we retrieve the price
            $price = $item_data['buy_now_price'];
            if ($price == null) {
                $price = 0;
            }


            // the max bid by default is the user bid because he's the one who purchased it 
            $current_bid = $user_bid;

            // unused variables
            $description = '';
            $owner_role = '';
            $duration_days = 0;


            // By default, query return only "Closed" items
            $time_left = "Closed";

            // for debug
            $today = new DateTime(AppTime::get_current_datetime());
            $end_at =  new DateTime($item_data['end_at']);
            $time_left_datetime = $end_at->diff($today);
            $time_left_debug = $time_left_datetime->invert;

            $purchases[] = new Item($user_bid, $item_data['item_id'], $title, $description, $owner_name, $owner_role, $main_image_url, $image_urls, $price, $current_bid, new DateTime(AppTime::get_current_datetime()), $end_at, $duration_days, $current_bid, 0, true, $is_auction, false, $has_buy_now, $buy_now_reached, $time_left, '');
        }

        return $purchases;
    }
}
