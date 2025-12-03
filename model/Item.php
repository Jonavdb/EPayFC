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
        //public int $image_count,
        public float $but_now_price,
        public float $starting_bid,
        //public float $current_bid,
        public DateTime $created_at,
        public int $duration_days,
        // public bool $has_bidder,
        // public bool $is_in_auction,
    ) {}

    public static function get_item_by_id(int $id)
    {
        $query = self::execute(
            "SELECT i.*, u.pseudo AS owner_pseudo, u.role AS owner_role
                                FROM items i 
                                JOIN users u on i.owner = u.id
                                WHERE i.id = :item_id",
            ["item_id" => $id]
        );
        $data = $query->fetch();

        if (!$data) {
            return false;
        }

        $query_pics = self::execute(
            "SELECT picture_path, priority
                                FROM item_pictures p
                                where p.item = :item_id
                                Order BY priority ASC",
            ["item_id" => $id]
        );
        $pictures = $query_pics->fetchAll(PDO::FETCH_COLUMN);

        return new Item(
            $data['title'],
            $data['description'],
            $data['owner_pseudo'],
            $data['owner_role'],
            $pictures[0],
            $pictures,
            $data['buy_now_price'] ?? 0.0,
            $data['starting_bid'],
            new DateTime($data['created_at']),
            $data['duration_days']
        );
    }
}
