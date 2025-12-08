<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Bid extends Model
{

    public function __construct(
        public int $owner,
        public int $item,
        public DateTime $created_at,
        public float $amount,
        public string $owner_pseudo
    ) {}

    public static function get_bid_by_item_id($id): array
    {

        $query = self::execute(
            "Select b.*, u.pseudo AS owner_pseudo
             FROM bids b
             JOIN users u on b.owner = u.id
             WHERE b.item = :item_id
             ORDER BY b.created_at DESC",
            ["item_id" => $id]
        );
        $data = $query->fetchAll();
        $bids_array = [];

        foreach ($data as $row) {
            $bids_array[] = new Bid(
                $row['owner'],
                $row['item'],
                new DateTime($row['created_at']),
                $row['amount'],
                $row['owner_pseudo']
            );
        }

        if (empty($bids_array)) {
            return self::simulate_bids();
        }

        return $bids_array;
    }


    private static function simulate_bids()
    {
        return [
            new Bid(99, 42, new DateTime('2025-11-11 21:55:03'), 229.00, 'Boris'),
            new Bid(98, 42, new DateTime('2025-11-11 10:25:18'), 214.00, 'Marc'),
            new Bid(97, 42, new DateTime('2025-11-10 20:36:50'), 198.00, 'Marc'),
        ];
    }
}
