<?php
require_once "framework/Controller.php";
require_once "model/Item.php";

class ControllerBrowseItems extends Controller {
    
    public function index(): void {
        $this->show_items();
    }

    public function show_items(): void {
        // static values to test
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
        $item = new Item(
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
        (new View("browse_items"))->show([
            "item" => $item
        ]);
    }
}