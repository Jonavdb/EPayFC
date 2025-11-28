<?php   

require_once "framework/Model.php";

class Item extends Model {
    
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
            ){}

    

}