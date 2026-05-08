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
}
