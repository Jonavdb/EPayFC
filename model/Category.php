<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Category extends Model {
    private int $id;
    private string $name;
    private int $priority;

    public function __construct(
        int $id,
        string $name,
        int $priority
    ) {
        $this->id           = $id;
        $this->name         = $name;
        $this->priority     = $priority;
    }

    public function getId(): int {
        return $this->id;
    }
    public function setId(int $id): void {
        $this->id = $id;
    }
    public function getName(): string {
        return $this->name;
    }
    public function setName(string $name): void {
        $this->name = $name;
    }
    public function getPriority(): int {
        return $this->priority;
    }
    public function setPriority(int $priority): void {
        $this->priority = $priority;
    }

    // Récupère toutes les catégories
    public static function get_all_categories(): array {
        $query = self::execute("SELECT * FROM categories ORDER BY priority ASC", []);
        $data = $query->fetchAll();
        $categories = [];

        foreach ($data as $row) {
            $categories[] = new Category(
                (int) $row['id'],
                $row['name'],
                (int) $row['priority'],
            );
        };

        return $categories;
    }
}
