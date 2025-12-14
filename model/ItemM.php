model
<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class ItemM extends Model
{
    public function __construct(
        public float $user_bid, // bid placed by the connected user on this item
        public bool $is_auction,
        public bool $has_buy_now,
        public array $image_urls,
        public string $main_image_url,
        public string $title,
        public string $owner_name,
        public float $price,
        public float $current_bid, // last amount bid on the item
        public string $time_left
    ) {}


    public static function get_user_bid_items(string $pseudo)
    {
        $user_bid_items = [];

        $query = self::execute(
            // Active Auctions bid on by user
            "SELECT DISTINCT vis.*
                from users u 
                join bids b on b.owner = u.id
                join v_items_status vis on vis.id = b.item
                where u.pseudo = :pseudo
                and :today BETWEEN vis.created_at AND vis.end_at
                order by vis.id",
            [
                "pseudo" => $pseudo,
                "today" => AppTime::get_current_datetime()
            ]
        );

        $data = $query->fetchAll();

        if (!$data) {
            return false;
        }

        foreach ($data as $item_data) {

            // we retrieve user bid on the item
            $user_bid = self::get_user_bid($item_data['id'], 4);

            // items on which user has placed bids are always in auction
            $is_auction = true;

            // we get the info whether or not the item can be purchased directly
            $has_buy_now = $item_data['has_buy_now'];
            $has_buy_now = $has_buy_now == 1 ? true : false;

            // we retrieve item pictures
            $query_image_urls = self::execute(
                "SELECT picture_path
                                FROM item_pictures p
                                where p.item = :item_id
                                Order BY priority ASC",
                ["item_id" => $item_data['id']]
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
                ["owner_id" => $item_data['owner']]
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

            // we retrieve the current bid -> max bid for this item
            $current_bid = $item_data['max_bid'];

            // we retrive time left 
            $today = new DateTime(AppTime::get_current_datetime());
            $end_at =  new DateTime($item_data['end_at']);
            $time_left_datetime = $end_at->diff($today);
            $time_left = $time_left_datetime->days . "d " . $time_left_datetime->h . "h";

            $user_bid_items[] = new ItemM($user_bid, $is_auction, $has_buy_now, $image_urls, $main_image_url, $title, $owner_name, $price, $current_bid, $time_left);
        }

        return $user_bid_items;
    }

    public static function get_user_bid(int $item_id, int $user_id)
    {
        $query = self::execute(
            "SELECT b.amount as bid
                                from bids b 
                                join users u on u.id = b.owner
                                join v_items_status vis on vis.id = b.item
                                where b.amount = vis.max_bid
                                and u.id = :user_id
                                and vis.id = :item_id;",
            [
                "item_id" => $item_id,
                "user_id" => $user_id
            ]
        );

        $data = $query->fetch();
        if (!$data) {
            return false;
        }

        return $data['bid'];
    }

    public static function get_user_available_items(string $pseudo)
    {
        $user_available_items = [];

        $query = self::execute(
            "SELECT DISTINCT vis.*
            FROM v_items_status vis
            WHERE :today BETWEEN vis.created_at AND vis.end_at
            AND 
            (
                -- CONDITION 1: Active Auctions NOT bid on by user
                (vis.is_auction = 1
                AND vis.id NOT IN (
                    SELECT DISTINCT vis_sub.id
                    FROM users u
                    JOIN bids b ON b.owner = u.id
                    JOIN v_items_status vis_sub ON vis_sub.id = b.item
                    WHERE u.pseudo = :pseudo
                )
                )
                
                OR
                
                -- CONDITION 2: Direct Sales NOT owned by user and NOT yet purchased
                (vis.is_direct_sale = 1
                AND vis.not_purchased_direct_sale = 1
                AND vis.owner NOT IN (
                    SELECT DISTINCT u.id
                    FROM users u
                    WHERE u.pseudo = :pseudo
                )
                )
            )

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
            $user_bid = 0;

            // we get info of whether the item is in auction or not
            $is_auction = $item_data['is_auction'];

            // available items are always purchasable
            $has_buy_now = true;

            // we retrieve item pictures
            $query_image_urls = self::execute(
                "SELECT picture_path
                                FROM item_pictures p
                                where p.item = :item_id
                                Order BY priority ASC",
                ["item_id" => $item_data['id']]
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
                ["owner_id" => $item_data['owner']]
            );

            $data_owner_name = $query_owner_name->fetch();

            if (!$data) {
                return false;
            }

            $owner_name = $data_owner_name['owner_name'];

            // we retrieve the price
            $price = $item_data['buy_now_price'];

            // we retrieve the current bid -> max bid for this item
            $has_bids = $item_data['has_bids'];
            if ($is_auction) {
                if ($has_bids) {
                    $current_bid = $item_data['max_bid'];
                } else {
                    $current_bid = 0.0;
                }
            }

            // we retrieve time left 
            $today = new DateTime(AppTime::get_current_datetime());
            $end_at =  new DateTime($item_data['end_at']);
            $time_left_datetime = $end_at->diff($today);
            $time_left = $time_left_datetime->days . "d " . $time_left_datetime->h . "h";

            $user_available_items[] = new ItemM($user_bid, $is_auction, $has_buy_now, $image_urls, $main_image_url, $title, $owner_name, $price, $current_bid, $time_left);
        }

        return $user_available_items;
    }
}
