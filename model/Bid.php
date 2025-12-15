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

        // if (empty($bids_array)) {
        //     return self::simulate_bids();
        // }

        return $bids_array;
    }

    public static function create_bid(string $user_pseudo, int $item_id, float $amount)
    {
        $now = AppTime::get_current_datetime();

        $query = "Insert into bids (owner, item, created_at, amount)
                  Values ((Select ID from Users where pseudo =:pseudo), :item, :created_at, :amount)";
        try {
            self::execute($query, [
                "pseudo" => $user_pseudo,
                "item" => $item_id,
                "created_at" => $now,
                "amount" => $amount
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
