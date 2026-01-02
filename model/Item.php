<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Item extends Model
{

    public function __construct(
        public float $user_bid,
        public int $id,
        public string $title,
        public string $description,
        public string $owner_name,
        public string $owner_role,
        public string $main_image_url,
        public array $image_urls,
        public float $buy_now_price,
        public float $starting_bid,
        public DateTime $created_at,
        public DateTime $end_at,
        public int $duration_days,
        public float $current_bid,
        public int $bid_count,
        public bool $has_bids,
        public bool $is_auction,
        public bool $is_direct_sale,
        public bool $has_buy_now,
        public bool $buy_now_reached,
        public string $time_left,
        public string $owner_picture
    ) {}

    public static function get_item_by_id(int $id)
    {
        $query = self::execute(
            "SELECT v.*, u.pseudo AS owner_pseudo, u.role AS owner_role, u.picture_path as owner_picture
             FROM v_items_status v  -- <--- C'est ici que la magie opère
             JOIN users u on v.owner = u.id
             WHERE v.id = :item_id",
            ["item_id" => $id]
        );
        $data = $query->fetch();

        if (!$data) {
            return false;
        }

        if (empty($data['owner_picture'])) {
            $owner_picture = 'test.png';
        } else {
            $owner_picture = $data['owner_picture'];
        }


        $query_pics = self::execute(
            "SELECT picture_path
             FROM item_pictures p
             where p.item = :item_id
             Order BY priority ASC",
            ["item_id" => $id]
        );
        $pictures = $query_pics->fetchAll(PDO::FETCH_COLUMN);
        $main_image_url = $pictures[0] ?? 'test.png';
        $created_at_object = new DateTime($data['created_at']);
        $end_date_object = new DateTime($data['end_at']);
        $current_price = ($data['max_bid'] !== null) ? (float)$data['max_bid'] : (float)$data['starting_bid'];

        return new Item(
            0,
            (int)$data['id'],
            $data['title'],
            $data['description'],
            $data['owner_pseudo'],
            $data['owner_role'],
            $main_image_url,
            $pictures,
            $data['buy_now_price'] ?? 0.0,
            $data['starting_bid'],
            $created_at_object,
            $end_date_object,
            $data['duration_days'],
            $current_price,
            $data['bid_count'],
            $data['has_bids'],
            $data['is_auction'],
            $data['is_direct_sale'],
            $data['has_buy_now'],
            $data['buy_now_reached'],
            '',
            $owner_picture
        );
    }

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

            // we get the info whether or not the item last bid exceeds the direct sale price
            $buy_now_reached = $item_data['buy_now_reached'];

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

            // unused variables
            $description = '';
            $owner_role = '';
            $duration_days = 0;

            $user_bid_items[] = new Item($user_bid, 0, $title, $description, $owner_name, $owner_role, $main_image_url, $image_urls, $price, $current_bid, new DateTime(AppTime::get_current_datetime()), $end_at, $duration_days, $current_bid, 0, true, false, false, $has_buy_now, $buy_now_reached, $time_left, '');
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
                -- CONDITION 1: Active Auctions NOT bid on by user AND Not owned by User
                (vis.is_auction = 1
                AND vis.owner != (SELECT id FROM users WHERE pseudo = :pseudo) -- Check: NOT Owned by User
                AND vis.id NOT IN ( -- Check: NOT Bid on by User
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
            $has_bids = $item_data['has_bids'];
            if ($is_auction) {
                if ($has_bids) {
                    $current_bid = $item_data['max_bid'];
                } else {
                    $current_bid = 0.0;
                }
            }
            // unused variables
            $description = '';
            $owner_role = '';
            $duration_days = 0;


            // we retrieve time left 
            $today = new DateTime(AppTime::get_current_datetime());
            $end_at =  new DateTime($item_data['end_at']);
            $time_left_datetime = $end_at->diff($today);
            $time_left = $time_left_datetime->days . "d " . $time_left_datetime->h . "h";

            $user_available_items[] = new Item($user_bid, 0, $title, $description, $owner_name, $owner_role, $main_image_url, $image_urls, $price, $current_bid, new DateTime(AppTime::get_current_datetime()), $end_at, $duration_days, $current_bid, 0, true, $is_auction, false, $has_buy_now, $buy_now_reached, $time_left, '');
        }

        return $user_available_items;
    }

    public static function change_pics_left(int $image_id, int $item_id)
    {
        $query = self::execute(
            "SELECT priority, picture_path 
         FROM item_pictures 
         WHERE item = :item_id 
         ORDER BY priority ASC",
            ["item_id" => $item_id]
        );
        $pictures = $query->fetchAll();

        if ($image_id > 0 && isset($pictures[$image_id])) {

            $current_img = $pictures[$image_id];
            $previous_img = $pictures[$image_id - 1];

            $current_priority = $current_img['priority'];
            $previous_priority = $previous_img['priority'];

            self::execute(
                "UPDATE item_pictures SET priority = -1 WHERE item = :item AND priority = :current_priority",
                ["item" => $item_id, "current_priority" => $current_priority]
            );

            self::execute(
                "UPDATE item_pictures SET priority = :current_priority WHERE item = :item AND priority = :previous_priority",
                ["item" => $item_id, "previous_priority" => $previous_priority, "current_priority" => $current_priority]
            );

            self::execute(
                "UPDATE item_pictures SET priority = :previous_priority WHERE item = :item AND priority = -1",
                ["item" => $item_id, "previous_priority" => $previous_priority]
            );
        }
    }

    public static function change_pics_right(int $image_id, int $item_id)
    {
        $query = self::execute(
            "SELECT priority, picture_path 
         FROM item_pictures 
         WHERE item = :item_id 
         ORDER BY priority ASC",
            ["item_id" => $item_id]
        );
        $pictures = $query->fetchAll();

        if ($image_id < count($pictures) - 1 && isset($pictures[$image_id])) {

            $current_img = $pictures[$image_id];
            $next_img = $pictures[$image_id + 1];

            $current_priority = $current_img['priority'];
            $next_priority = $next_img['priority'];

            self::execute(
                "UPDATE item_pictures SET priority = -1 WHERE item = :item AND priority = :current_priority",
                ["item" => $item_id, "current_priority" => $current_priority]
            );

            self::execute(
                "UPDATE item_pictures SET priority = :current_priority WHERE item = :item AND priority = :next_priority",
                ["item" => $item_id, "next_priority" => $next_priority, "current_priority" => $current_priority]
            );

            self::execute(
                "UPDATE item_pictures SET priority = :next_priority WHERE item = :item AND priority = -1",
                ["item" => $item_id, "next_priority" => $next_priority]
            );
        }
    }

    public static function delete_images(string $filename, int $item_id)
    {
        $query = self::execute(
            "SELECT priority, picture_path 
         FROM item_pictures 
         WHERE item = :item_id",
            ["item_id" => $item_id]
        );
        $pictures = $query->fetchAll();

        $image_to_delete = null;

        foreach ($pictures as $pic) {
            if (basename($pic['picture_path']) === $filename) {
                $image_to_delete = $pic;
                break;
            }
        }

        if ($image_to_delete) {

            self::execute(
                "DELETE FROM item_pictures WHERE item = :item_id AND priority = :priority",
                [
                    "item_id" => $item_id,
                    "priority" => $image_to_delete['priority']
                ]
            );
            self::execute(
                "UPDATE item_pictures SET priority = priority - 1 WHERE item = :item_id AND priority > :deleted_priority",
                [
                    "item_id" => $item_id,
                    "deleted_priority" => $image_to_delete['priority']
                ]
            );
        }
    }

    public static function add_picture(int $item_id, string $path)
    {

        $query = self::execute(
            "SELECT MAX(priority) as max_p FROM item_pictures WHERE item = :item_id",
            ["item_id" => $item_id]
        );
        $data = $query->fetch();
        $next_priority = ($data['max_p']) ? $data['max_p'] + 1 : 1;

        self::execute(
            "INSERT INTO item_pictures (item, picture_path, priority) VALUES (:item, :path, :priority)",
            [
                "item"     => $item_id,
                "path"     => $path,
                "priority" => $next_priority
            ]
        );
    }
}
