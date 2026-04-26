<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Bid extends Model {
    private int $owner;
    private int $item;
    private DateTime $created_at;
    private float $amount;
    private string $owner_pseudo;

    public function __construct(int $owner, int $item, DateTime $created_at, float $amount, string $owner_pseudo) {
        $this->owner        = $owner;
        $this->item         = $item;
        $this->created_at   = $created_at;
        $this->amount       = $amount;
        $this->owner_pseudo = $owner_pseudo;
    }


    public function get_owner(): int {
        return $this->owner;
    }
    public function get_item(): int {
        return $this->item;
    }
    public function get_created_at(): DateTime {
        return $this->created_at;
    }
    public function get_amount(): float {
        return $this->amount;
    }
    public function get_owner_pseudo(): string {
        return $this->owner_pseudo;
    }

    public function validate(): array {
        $errors = [];

        if ($this->amount <= 0) {
            $errors[] = "Le montant de l'enchère doit être supérieur à 0.";
        }

        if (empty($this->owner_pseudo)) {
            $errors[] = "L'enchère doit être associée à un utilisateur.";
        }

        return $errors;
    }

    public static function get_bid_by_item_id($id): array {

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

    public static function create_bid(string $user_pseudo, int $item_id, float $amount): bool {
        $now = AppTime::get_current_datetime();

        $query = "Insert into bids (owner, item, created_at, amount)
                  Values ((Select id from users where pseudo =:pseudo), :item, :created_at, :amount)";
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
