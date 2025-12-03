<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Item extends Model
{

    public function __construct(
        public string $title,
        public string $description,
        public string $owner_name,
        public string $owner_status,
        public string $main_image_url,
        public array $image_urls,
        public int $image_count,
        public float $price,
        public float $current_bid,
        public DateTime $created_at,
        public int $duration_days,
        public bool $has_bidder,
        public bool $is_in_auction,
    ) {}

    public static function get_item_by_id(int $id)
    {
        if ($id === 42) {
            $now = AppTime::get_current_datetime();
            $created_at_test = new DateTime($now);
            $image_urls_test = [
                'test.jpg',
                'test.jpg'
            ];
            return new Item(
                'Piano numérique Yamaha P-45',
                'Piano a touches lestées',
                'Xavier',
                'Member',
                'test.jpg',
                $image_urls_test,
                count($image_urls_test),
                355.00,
                220.00,
                $created_at_test,
                15,
                true,
                true
            );
        }
        return false;
    }
}
