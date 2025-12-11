model
<?php

require_once "framework/Model.php";

class ItemM extends Model
{

    public function __construct(
        public string $title,
        public string $owner_name,
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

    public static function get_user_bid_items(string $pseudo)
    {
        $query = self::execute(
            "SELECT i.*
                FROM users u 
                jOIN bids b on b.owner = u.id
                JOIN items i on i.id = b.item
                where u.pseudo = :pseudo
                ORDER by id",
            ["pseudo" => $pseudo]
        );

        $data = $query->fetchAll();

        if (!$data) {
            return [];
        }

        return $data;
    }
}
