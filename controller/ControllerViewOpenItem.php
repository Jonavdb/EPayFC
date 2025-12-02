<?php

require_once "framework/Controller.php";
require_once "utils/AppTime.php";

class ControllerViewOpenItem extends Controller
{

    public function index(): void
    {
        if (!Configuration::is_dev()) {
            $this->redirect();
        }

        $item = [
            'title' => 'Piano numérique Yamaha P-45',
            'description' => 'Touches lestées, pédale incluse.',
            'current_bid' => 229.00,
            'buy_now_price' => 385.00,
            'start_date' => '10/11/2025 04:16:04',
            'end_date' => '17/11/2025 04:16:04',
            'seller' => 'Xavier',
            'status' => 'Member'
        ];

        $bids = [
            ['user' => 'Boris', 'amount' => 229.00, 'time' => '11/11/2025 21:55:03', 'active' => true],
            ['user' => 'Marc', 'amount' => 214.00, 'time' => '11/11/2025 10:25:18', 'active' => false],
            ['user' => 'Marc', 'amount' => 198.00, 'time' => '10/11/2025 20:36:50', 'active' => false],
            ['user' => 'Marc', 'amount' => 255.00, 'time' => '10/11/2025 20:36:50', 'active' => false]

        ];

        (new View("open_item"))->show([
            "item" => $item,
            "bids" => $bids
        ]);
    }
}
