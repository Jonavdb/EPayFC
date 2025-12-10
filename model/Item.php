<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Item extends Model
{

    public function __construct(
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
        public bool $is_direct_sale
    ) {}

    public static function get_item_by_id(int $id)
    {
        $query = self::execute(
            "SELECT v.*, u.pseudo AS owner_pseudo, u.role AS owner_role
             FROM v_items_status v  -- <--- C'est ici que la magie opère
             JOIN users u on v.owner = u.id
             WHERE v.id = :item_id",
            ["item_id" => $id]
        );
        $data = $query->fetch();

        if (!$data) {
            return false;
        }

        $query_pics = self::execute(
            "SELECT picture_path
             FROM item_pictures p
             where p.item = :item_id
             Order BY priority ASC",
            ["item_id" => $id]
        );
        $pictures = $query_pics->fetchAll(PDO::FETCH_COLUMN);
        $main_image_url = $pictures[0] ?? 'test.jpg';
        $created_at_object = new DateTime($data['created_at']);
        $end_date_object = new DateTime($data['end_at']);
        $current_price = ($data['max_bid'] !== null) ? (float)$data['max_bid'] : (float)$data['starting_bid'];

        return new Item(
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
            $data['is_direct_sale']
        );
    }
}
