<?php

require_once "framework/Model.php";

enum MoveDirection: string {
    case UP = 'up';
    case DOWN = 'down';
}


class Category extends Model {
    private int $id;
    private string $name;
    private int $priority;
    private bool $is_empty;


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
        $name_sanitized = trim($name);
        $this->name = $name_sanitized;
        self::execute(
            "UPDATE categories set name = :name where id = :id",
            [
                "name" => $this->name,
                "id" => $this->get_id()
            ]
        );
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

    public function get_items_count(): int {
        $res = -1;
        $query = self::execute(
            "SELECT distinct * FROM item_categories ic
            WHERE category = :categ_id",
            ["categ_id" => $this->get_id()]
        );

        $data = $query->fetchAll();

        if ($data) {
            $res = count($data);
        }

        return $res <= 0 ? 0 : $res;
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

    public static function get_category_from_name(string $name, ?int $exclude_id = null): ?Category {

        // 2. Start with your base query and parameters
        $sql = "SELECT * FROM categories c WHERE c.name = :name";
        $params = ["name" => $name];

        // 3. Dynamically inject the exclusion condition ONLY if an ID was passed
        if ($exclude_id !== null) {
            $sql .= " AND c.id != :exclude_id";
            $params["exclude_id"] = $exclude_id;
        }

        // 4. Execute using the dynamic strings/arrays
        $query = self::execute($sql, $params);

        $data = $query->fetch();

        if (!$data) {
            return null;
        }

        $id = (int)$data['id'];
        $name =  $data['name'];
        $priority = (int)$data['priority'];
        $is_empty = self::get_items_count_from_category($id) <= 0;

        return new self(
            $id,
            $name,
            $priority,
            $is_empty
        );
    }

    public static function get_next_category_above(int $priority): Category {
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

    public static function get_next_category_below(int $priority): Category {
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

    public static function add(string $name): Category {
        // COALESCE ensures that if the table is totally empty, it returns 0 instead of null.
        $query = self::execute(
            "SELECT COALESCE(MAX(priority), 0) as max_priority FROM categories",
            []
        );
        $data = $query->fetch();
        $next_priority = (int)$data['max_priority'] + 1;

        // Execute the insert
        self::execute(
            "INSERT INTO categories (name, priority) VALUES (:name, :priority)",
            [
                "name"     => $name,
                "priority" => $next_priority
            ]
        );

        return self::get_category_from_name($name);
    }

    public static function delete(int $id): bool {
        // on verifie s'il y a des items dans la categorie
        $query = self::execute(
            "SELECT distinct * FROM item_categories ic
            WHERE category = :categ_id",
            ["categ_id" => $id]
        );

        // on fait rien si la categorie n'est pas vide
        $data = $query->fetchAll();
        if ($data)
            return false;

        // supprime la categorie si elle est vide
        self::execute(
            "DELETE FROM categories WHERE id = :id",
            ["id" => $id]
        );

        return true;
    }

    // Fonction qui echange de priorité avec une categorie plus haut/plus bas (NO NS)
    public static function move(int $from_id, MoveDirection $direction) {
        // on détermine la direction du mouvement
        if (in_array($direction, [MoveDirection::UP, MoveDirection::DOWN])) {
            $from = Category::get_category_from_id($from_id);
            $to   = $direction === MoveDirection::UP
                ? Category::get_next_category_above($from->get_priority())  // vers la categorie juste au dessus  dans la liste
                : Category::get_next_category_below($from->get_priority()); // vers la categorie juste en dessous dans la liste

            // on récupère les priorités
            $priority_from = $from->get_priority();
            $priority_to   = $to->get_priority();

            // on échange les priorités
            $to->set_priority(-1);
            $from->set_priority($priority_to);
            $to->set_priority($priority_from);
        }
    }

    // Fonction qui réorganise les priorités dans la db après un drag (JS)
    public static function update_order(array $new_order): array {
        // on met toutes les priorité de de la base de données à leur valeur négative temporairement
        foreach (Category::get_categories() as $category) {
            $category->set_priority(-$category->get_priority());
        }

        // on assigne à chaque categorie de l'id associé sa nouvelle priorité (index + 1 parce que les priorités commence à 1, pas à 0)
        foreach ($new_order as $index => $id) {
            $categ = Category::get_category_from_id($id);
            $categ->set_priority($index + 1);
        }

        // on retourne la nouvelle liste de categories reordonnées (uniquement l'id)
        return array_map(fn($category) => $category->get_id(), Category::get_categories());
    }

    public static function validate(string $name): array {

        $name_sanitized = trim($name);
        $errors = [];

        $is_empty = empty($name_sanitized);

        $is_valid_format = preg_match('/^[A-Z][a-zA-Z]*$/', $name_sanitized);

        $length = strlen($name_sanitized);
        $is_valid_size = ($length >= 3 && $length <= 25);

        $query = self::execute(
            "SELECT * from categories where name = :name_sanitized",
            ["name_sanitized" => $name_sanitized]
        );
        $is_name_unique = !$query->fetch();


        if ($is_empty) {
            $errors['empty'] = "Champ vide.";
        }

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
