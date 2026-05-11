<?php

require_once "framework/Model.php";

class Category extends Model {
    private int $id;
    private string $name;
    private int $priority;

    public function __construct(int $id, string $name, int $priority) {
        $this->id       = $id;
        $this->name     = $name;
        $this->priority = $priority;
    }

    public function get_id(): int {
        return $this->id;
    }
    public function get_name(): string {
        return $this->name;
    }
    public function get_priority(): int {
        return $this->priority;
    }

    public static function get_categories(): array {
        $query = self::execute("SELECT * FROM categories ORDER BY priority ASC", []);
        $data = $query->fetchAll();
        $categories = [];

        foreach ($data as $row) {
            $categories[] = new self(
                (int)$row['id'],
                $row['name'],
                (int)$row['priority']
            );
        };
        return $categories;
    }

    public static function get_item_categories_by_id(int $item_id): array {
        $query = self::execute(
            "SELECT categories.id
                FROM categories, items, item_categories
                WHERE categories.id = item_categories.category
                AND items.id = item_categories.item
                AND items.id = :item_id;",
            ["item_id" => $item_id]
        );


        $data = $query->fetchAll();
        $item_categories = [];

        if (!$data) {
            return [];
        } else {
            foreach ($data as $row) {
                $item_categories[] = (int)$row['id'];
            };

            return $item_categories;
        }
    }

    public static function get_categories_names(int $item_id): array {
        $query = self::execute(
            "SELECT categories.name
                FROM categories, items, item_categories
                WHERE categories.id = item_categories.category
                AND items.id = item_categories.item
                AND items.id = :item_id
                ORDER BY categories.name ASC;",
            ["item_id" => $item_id]
        );

        $data = $query->fetchAll();
        $item_categories = [];

        if (!$data) {
            return [];
        } else {
            foreach ($data as $row) {
                $item_categories[] = $row['name'];
            };

            return $item_categories;
        }
    }
}
