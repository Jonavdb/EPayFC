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
        $query = self::execute(
            "SELECT *
            FROM categories
            ORDER BY priority ASC",
            []
        );

        $categories = [];
        foreach ($query->fetchAll() as $row) {
            $categories[] = new self(
                (int)$row['id'],
                $row['name'],
                (int)$row['priority']
            );
        }
        return $categories;
    }

    public static function add(string $name): void {
        $sql = "INSERT INTO categories (name, priority) VALUES (:name, :priority)";
        self::execute($sql, [
            "name"     => $name,
            "priority" => count(Category::get_categories()) + 1
        ]);
    }

    public static function validate(string $name): array {

        $name_sanitized = trim($name);
        $errors = [];

        $is_valid_format = preg_match('/^[A-Z][a-zA-Z]*$/', $name_sanitized);

        $length = strlen($name_sanitized);
        $is_valid_size = ($length >= 3 && $length <= 25);

        $query = self::execute(
            "SELECT * from categories where name = :name_sanitized",
            ["name_sanitized" => $name]
        );
        $is_name_unique = !$query->fetch();


        if (!$is_valid_format) {
            $errors['bad_format'] = "Format invalide : commencez par une majuscule, sans espaces ni chiffres.";
        }

        if (!$is_valid_size) {
            $errors['bad_size'] = "Le nom doit contenir entre 3 et 25 caractères.";
        }

        if (!$is_name_unique) {
            $errors['not_unique'] = "Cette categorie existe déjà.";
        }

        return $errors;
    }
}
