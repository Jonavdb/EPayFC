<?php

require_once "framework/Model.php";

enum SwapType: string {
    case UP = 'up';
    case DOWN = 'down';
}


class Category extends Model {
    private int $id;
    private string $name;
    private int $priority;
    private bool $is_empty;

    // enum swap type (UP,Down)

    public function __construct(int $id, string $name, int $priority, bool $is_empty) {
        $this->id       = $id;
        $this->name     = $name;
        $this->priority = $priority;
        $this->is_empty = $is_empty;
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
    public function is_empty(): bool {
        return $this->is_empty;
    }

    public function set_id(int $id): void {
        $this->id = $id;
        // update database
    }

    public function set_name(string $name): void {
        $this->name = $name;
        // update database
    }

    public function set_priority(int $priority): void {
        $this->priority = $priority;
        self::execute(
            "UPDATE categories set priority = :priority where id = :id",
            [
                "priority" => $priority,
                "id" => $this->get_id()
            ]
        );
    }

    public function set_is_empty(bool $is_empty): void {
        $this->is_empty = $is_empty;
    }

    public static function get_categories(): array {
        $query = self::execute("SELECT * FROM categories ORDER BY priority ASC", []);
        $data = $query->fetchAll();
        $categories = [];
        foreach ($data as $row) {
            $id = (int)$row['id'];
            $name =  $row['name'];
            $priority = (int)$row['priority'];
            $is_empty = self::get_items_count_from_category($id) <= 0;

            $categories[] = new self(
                $id,
                $name,
                $priority,
                $is_empty
            );
        };
        return $categories;
    }

    public static function get_items_count_from_category(int $categ_id): int {
        $res = -1;

        $query = self::execute(
            "SELECT distinct * FROM item_categories ic
            WHERE category = :categ_id",
            ["categ_id" => $categ_id]
        );

        $data = $query->fetchAll();

        if ($data) {
            $res = count($data);
        }

        return $res;
    }

    public static function get_item_categories(int $item_id): array {
        $item_categories = [];

        $query = self::execute(
            "SELECT name
            FROM categories, items, item_categories
            WHERE categories.id = item_categories.category
            AND items.id = item_categories.item
            AND items.id = :item_id;",
            ["item_id" => $item_id]
        );

        $data = $query->fetchAll();

        if (!$data) {
            return [];
        } else {
            sort($data);    // trie alphabétiquement

            foreach ($data as $row) {
                array_push($item_categories, $row['name']);
            };
        }

        return $item_categories;
    }

    public static function get_category_from_id(int $categ_id): Category {
        $res = null;
        $query = self::execute(
            "SELECT * FROM categories c where c.id = :categ_id",
            ["categ_id"  => $categ_id]
        );

        $data = $query->fetch();

        $id = (int)$data['id'];
        $name =  $data['name'];
        $priority = (int)$data['priority'];
        $is_empty = self::get_items_count_from_category($id) <= 0;

        if ($data) {
            $res = new self(
                $id,
                $name,
                $priority,
                $is_empty
            );
        }

        return $res;
    }

    public static function get_category_with_next_lower_priority(int $priority): Category {
        $res = null;
        $query = self::execute(
            "SELECT * FROM categories c 
                where c.priority < :priority
                order by c.priority DESC
                LIMIT 1",
            ["priority"  => $priority]
        );

        $data = $query->fetch();

        $id = (int)$data['id'];
        $name =  $data['name'];
        $priority = (int)$data['priority'];
        $is_empty = self::get_items_count_from_category($id) <= 0;

        if ($data) {
            $res = new self(
                $id,
                $name,
                $priority,
                $is_empty
            );
        }

        return $res;
    }

    public static function get_category_with_next_higher_priority(int $priority): Category {
        $res = null;
        $query = self::execute(
            "SELECT * FROM categories c 
                where c.priority > :priority
                order by c.priority ASC
                LIMIT 1",
            ["priority"  => $priority]
        );

        $data = $query->fetch();

        $id = (int)$data['id'];
        $name =  $data['name'];
        $priority = (int)$data['priority'];
        $is_empty = self::get_items_count_from_category($id) <= 0;

        if ($data) {
            $res = new self(
                $id,
                $name,
                $priority,
                $is_empty
            );
        }

        return $res;
    }

    // Category::swap(int $id1, int $id2, SwapType type); 

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
