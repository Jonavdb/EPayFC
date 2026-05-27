<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";
require_once "model/ItemPicture.php";

class Item extends Model {
    private float $user_bid;
    private ?string $bidder_name;
    private int $id;
    private string $title;
    private string $description;
    private string $owner_name;
    private string $owner_role;
    private string $main_image_url;
    private array $image_urls;
    private array $pictures;
    private ?float $buy_now_price;
    private ?float $starting_bid;
    private DateTime $created_at;
    private DateTime $end_at;
    private int $duration_days;
    private ?float $current_bid;
    private int $bid_count;
    private bool $has_bids;
    private bool $is_auction;
    private bool $is_direct_sale;
    private bool $has_buy_now;
    private bool $buy_now_reached;
    private string $time_left;
    private string $owner_picture;

    public function __construct(
        float $user_bid,
        ?string $bidder_name,
        int $id,
        string $title,
        string $description,
        string $owner_name,
        string $owner_role,
        string $main_image_url,
        array $image_urls,
        ?float $buy_now_price,
        ?float $starting_bid,
        DateTime $created_at,
        DateTime $end_at,
        int $duration_days,
        ?float $current_bid,
        int $bid_count,
        bool $has_bids,
        bool $is_auction,
        bool $is_direct_sale,
        bool $has_buy_now,
        bool $buy_now_reached,
        string $time_left,
        string $owner_picture
    ) {
        $this->user_bid      = $user_bid;
        $this->bidder_name   = $bidder_name;
        $this->id            = $id;
        $this->title         = $title;
        $this->description   = $description;
        $this->owner_name    = $owner_name;
        $this->owner_role    = $owner_role;
        $this->main_image_url = $main_image_url;
        $this->image_urls    = $image_urls;
        $this->buy_now_price = $buy_now_price;
        $this->starting_bid  = $starting_bid;
        $this->created_at    = $created_at;
        $this->end_at        = $end_at;
        $this->duration_days = $duration_days;
        $this->current_bid   = $current_bid;
        $this->bid_count     = $bid_count;
        $this->has_bids      = $has_bids;
        $this->is_auction    = $is_auction;
        $this->is_direct_sale = $is_direct_sale;
        $this->has_buy_now   = $has_buy_now;
        $this->buy_now_reached = $buy_now_reached;
        $this->time_left     = $time_left;
        $this->owner_picture = $owner_picture;

        $this->pictures = [];
        foreach ($image_urls as $priority => $path) {
            $this->pictures[] = new ItemPicture($id, $path, $priority);
        }
    }

    public function get_user_bid_amount(): float {
        return $this->user_bid;
    }
    public function get_bidder_name(): ?string {
        return $this->bidder_name;
    }
    public function get_id(): int {
        return $this->id;
    }
    public function get_title(): string {
        return $this->title;
    }
    public function get_description(): string {
        return $this->description;
    }
    public function get_owner_name(): string {
        return $this->owner_name;
    }
    public function get_owner_role(): string {
        return $this->owner_role;
    }
    public function get_main_image_url(): string {
        return $this->main_image_url;
    }
    public function get_image_urls(): array {
        return $this->image_urls;
    }
    public function get_pictures(): array {
        return $this->pictures;
    }
    public function get_buy_now_price(): ?float {
        return $this->buy_now_price;
    }
    public function get_starting_bid(): ?float {
        return $this->starting_bid;
    }
    public function get_created_at(): DateTime {
        return $this->created_at;
    }
    public function get_end_at(): DateTime {
        return $this->end_at;
    }
    public function get_duration_days(): int {
        return $this->duration_days;
    }
    public function get_current_bid(): ?float {
        return $this->current_bid;
    }
    public function get_bid_count(): int {
        return $this->bid_count;
    }
    public function get_has_bids(): bool {
        return $this->has_bids;
    }
    public function get_is_auction(): bool {
        return $this->is_auction;
    }
    public function get_is_direct_sale(): bool {
        return $this->is_direct_sale;
    }
    public function get_has_buy_now(): bool {
        return $this->has_buy_now;
    }
    public function get_buy_now_reached(): bool {
        return $this->buy_now_reached;
    }
    public function get_time_left(): string {
        return $this->time_left;
    }
    public function get_owner_picture(): string {
        return $this->owner_picture;
    }

    public function set_main_image_url(string $url): void {
        $this->main_image_url = $url;
    }
    public function set_id(int $id): void {
        $this->id = $id;
    }
    public function set_title(string $title): void {
        $this->title = $title;
    }
    public function set_description(string $desc): void {
        $this->description = $desc;
    }
    public function set_duration_days(int $days): void {
        $this->duration_days = $days;
    }
    public function set_starting_bid(?float $bid): void {
        $this->starting_bid = $bid;
    }
    public function set_buy_now_price(?float $price): void {
        $this->buy_now_price = $price;
    }
    public function set_is_auction(bool $v): void {
        $this->is_auction = $v;
    }
    public function set_is_direct_sale(bool $v): void {
        $this->is_direct_sale = $v;
    }
    public function set_has_buy_now(bool $v): void {
        $this->has_buy_now = $v;
    }

    public function validate(): array {
        $errors = [];

        if (empty(trim($this->title))) {
            $errors['title'] = "Le titre ne peut pas être vide.";
        }

        if (empty(trim($this->description))) {
            $errors['description'] = "La description ne peut pas être vide.";
        }

        if ($this->duration_days <= 0) {
            $errors['duration_days'] = "La durée doit être supérieure à 0 jour.";
        }

        if ($this->is_auction && ($this->starting_bid === null || $this->starting_bid <= 0)) {
            $errors['starting_bid'] = "Le prix de départ d'une enchère doit être supérieur à 0.";
        }

        if ($this->has_buy_now && ($this->buy_now_price === null || $this->buy_now_price <= 0)) {
            $errors['buy_now_price'] = "Le prix d'achat immédiat doit être supérieur à 0.";
        }

        return $errors;
    }


    // paramètre optionnel rajouté pour le champ de recherche de l'itération 2
    public static function get_items_by_owner(int $owner_id, ?string $search = null, ?int $category_id = null): array {
        // Récupère les items du propriétaire, triés par date de fin calculée
        $sql = "SELECT * FROM v_items_status WHERE owner = :owner";
        $params = ["owner" => $owner_id];

        // si l'utilisateur a tapé quelque chose, on ajoute les conditions LIKE
        if ($search !== null && trim($search) !== '') {
            $sql .= " AND (title LIKE :search OR description LIKE :search)";

            $params['search'] = '%' . trim($search) . '%';
        }

        if ($category_id !== null) {
            $sql .= " AND id in (SELECT item FROM item_categories WHERE category = :category_id)";
            $params['category_id'] = $category_id;
        }

        $sql .= " ORDER BY end_at ASC";

        // exécute SQL dynamique avec les paramètres
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        $items = [];

        // unused variables
        $user_bid = 0.0;
        $owner_role = '';
        $main_image_url = '';
        $image_urls = [];
        $owner_picture = '';
        $time_left = '';

        foreach ($data as $row) {
            // GESTION DES IMAGES
            $query_image_urls = self::execute(
                "SELECT picture_path FROM item_pictures p WHERE p.item = :item_id ORDER BY priority ASC",
                ["item_id" => $row['id']]
            );

            $image_urls = $query_image_urls->fetchAll(PDO::FETCH_COLUMN);

            // Image par défaut (Placeholder)
            $main_image_url = "css/images/default_item_image.png";

            if (!empty($image_urls)) {
                $first_image = $image_urls[0];

                // On génère le chemin vers la miniature (_thumbnail)
                $path_parts = pathinfo($first_image);

                // Sécurité : on vérifie que pathinfo a réussi
                if (isset($path_parts['dirname'], $path_parts['filename'], $path_parts['extension'])) {
                    $main_image_url = $path_parts['dirname'] . '/' .
                        $path_parts['filename'] .
                        '_thumbnail.' .
                        $path_parts['extension'];
                } else {
                    // Si le chemin est bizarre, on garde l'original
                    $main_image_url = $first_image;
                }
            } else {
                // Si pas d'image, on initialise un tableau vide pour éviter les erreurs plus tard
                $image_urls = [];
            }

            // PRÉPARATION DES DONNÉES (Dates & Prix)
            $created_at_object = new DateTime($row['created_at']);
            $end_at_object = new DateTime($row['end_at']);
            $current_bid = isset($row['max_bid']) ? (float)$row['max_bid'] : 0.0;

            // CRÉATION DE L'OBJET ITEM
            $items[] = new Item(
                $user_bid,
                null,
                $row['id'],
                $row['title'],
                $row['description'],
                $row['owner'],
                $owner_role,
                $main_image_url,
                $image_urls,
                (float)$row['buy_now_price'],
                (float)$row['starting_bid'],
                $created_at_object,
                $end_at_object,
                $row['duration_days'],
                $current_bid,
                $row['bid_count'],
                $row['has_bids'],
                $row['is_auction'],
                $row['is_direct_sale'],
                $row['has_buy_now'],
                $row['buy_now_reached'],
                $time_left,
                $owner_picture
            );
        }

        return $items;
    }

    public static function get_item_by_id(int $id): Item|false {
        $query = self::execute(
            "SELECT v.*, u.pseudo AS owner_pseudo, u.role AS owner_role, u.picture_path as owner_picture
            FROM v_items_status v 
            JOIN users u on v.owner = u.id
            WHERE v.id = :item_id",
            ["item_id" => $id]
        );
        $data = $query->fetch();

        if (!$data) {
            return false;
        }

        if (empty($data['owner_picture'])) {
            $owner_picture = 'css/images/default_user_image.jpg';
        } else {
            $owner_picture = $data['owner_picture'];
        }

        $query_pics = self::execute(
            "SELECT picture_path
            FROM item_pictures p
            where p.item = :item_id
            Order BY priority ASC",
            ["item_id" => $id]
        );

        $pictures = $query_pics->fetchAll(PDO::FETCH_COLUMN);
        $main_image_url = $pictures[0] ?? 'css/images/default_item_image.png';
        $created_at_object = new DateTime($data['created_at']);
        $end_date_object = new DateTime($data['end_at']);
        $current_price = ($data['max_bid'] !== null) ? (float)$data['max_bid'] : (float)$data['starting_bid'];
        return new Item(
            0,
            null,
            (int)$data['id'],
            $data['title'],
            $data['description'],
            $data['owner_pseudo'],
            $data['owner_role'],
            $main_image_url,
            $pictures,
            $data['buy_now_price'] ?? 0.0,
            $data['starting_bid'],
            $created_at_object,
            $end_date_object,
            $data['duration_days'],
            $current_price,
            $data['bid_count'],
            $data['has_bids'],
            $data['is_auction'],
            $data['is_direct_sale'],
            $data['has_buy_now'],
            $data['buy_now_reached'],
            '',
            $owner_picture
        );
    }

    public static function get_item_by_title_and_owner(string $title, int $owner_id) {
        // 1. Scope the query by BOTH title AND owner ID
        $query = self::execute(
            "SELECT v.*, u.pseudo AS owner_pseudo, u.role AS owner_role, u.picture_path as owner_picture
        FROM v_items_status v 
        JOIN users u on v.owner = u.id
        WHERE v.title = :title AND v.owner = :owner_id",
            [
                "title" => $title,
                "owner_id" => $owner_id
            ]
        );
        $data = $query->fetch();

        if (!$data) {
            return false;
        }

        if (empty($data['owner_picture'])) {
            $owner_picture = 'css/images/default_user_image.jpg';
        } else {
            $owner_picture = $data['owner_picture'];
        }

        $query_pics = self::execute(
            "SELECT picture_path
        FROM item_pictures p
        WHERE p.item = :item_id
        ORDER BY priority ASC",
            ["item_id" => (int)$data['id']]
        );

        $pictures = $query_pics->fetchAll(PDO::FETCH_COLUMN);
        $main_image_url = $pictures[0] ?? 'css/images/default_item_image.png';

        $created_at_object = new DateTime($data['created_at']);
        $end_date_object = new DateTime($data['end_at']);
        $current_price = ($data['max_bid'] !== null) ? (float)$data['max_bid'] : (float)$data['starting_bid'];

        return new Item(
            0,
            null,
            (int)$data['id'],
            $data['title'],
            $data['description'],
            $data['owner_pseudo'],
            $data['owner_role'],
            $main_image_url,
            $pictures,
            $data['buy_now_price'] ?? 0.0,
            $data['starting_bid'],
            $created_at_object,
            $end_date_object,
            $data['duration_days'],
            $current_price,
            $data['bid_count'],
            $data['has_bids'],
            $data['is_auction'],
            $data['is_direct_sale'],
            $data['has_buy_now'],
            $data['buy_now_reached'],
            '',
            $owner_picture
        );
    }

    // Delete item
    public static function delete(int $id): bool {
        // cible directement le dossier de l'item
        $baseDir = dirname(__DIR__) . "/";
        $itemDirectory = $baseDir . "uploads/items/" . $id;

        // si le dossier existe, on le vide puis on le supprime
        if (is_dir($itemDirectory)) {
            // glob() récupère TOUS les fichiers (l'image ET le thumbnail)
            $files = glob($itemDirectory . '/*');

            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file); // supprime physiquement chaque fichier
                }
            }

            // maintenant que le dossier est vide, on le supprime
            @rmdir($itemDirectory);
        }

        // nettoie proprement la base de données
        self::execute("DELETE FROM item_pictures WHERE item = :item_id", ["item_id" => $id]);
        self::execute("DELETE FROM bids WHERE item = :item_id", ["item_id" => $id]);
        self::execute("DELETE FROM items WHERE id = :item_id", ["item_id" => $id]);

        return true;
    }

    public static function get_user_bid_items(int $id, ?string $search = null, ?int $category_id = null): array {
        $user_bid_items = [];

        $sql = "SELECT DISTINCT vis.*
                FROM bids b
                JOIN v_items_status vis ON vis.id = b.item
                JOIN users seller ON vis.owner = seller.id
                WHERE b.owner = :id
                AND :today BETWEEN vis.created_at AND vis.end_at
                AND vis.buy_now_reached = 0";

        $params = [
            "id" => $id,
            "today" => AppTime::get_current_datetime()
        ];

        // SI on a une recherche en cours, on ajoute les conditions
        if ($search !== null && trim($search) !== '') {
            $sql .= " AND (
                        vis.title LIKE :search 
                        OR seller.pseudo LIKE :search
                        OR vis.description LIKE :search
                      )";

            $params['search'] = '%' . trim($search) . '%';
        }

        if ($category_id !== null) {
            $sql .= " AND vis.id in (SELECT item FROM item_categories WHERE category = :category_id)";
            $params['category_id'] = $category_id;
        }

        $sql .= " ORDER BY vis.end_at";

        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        if (!$data) {
            return [];
        }

        foreach ($data as $item_data) {
            $user_bid = Item::get_user_bid($item_data['id'], $id);
            if (is_null($user_bid)) {
                $user_bid = 0;
            }

            $has_buy_now = $item_data['has_buy_now'] == 1 ? true : false;
            $buy_now_reached = $item_data['buy_now_reached'];

            $query_image_urls = self::execute(
                "SELECT picture_path FROM item_pictures p WHERE p.item = :item_id ORDER BY priority ASC",
                ["item_id" => $item_data['id']]
            );
            $image_urls = (array) $query_image_urls->fetchAll(PDO::FETCH_COLUMN);

            $main_image_url = "";
            if (!empty($image_urls)) {
                $main_image_url = $image_urls[0];
                $path_parts = pathinfo($main_image_url);
                $main_image_url = $path_parts['dirname'] . '/' . $path_parts['filename'] . '_thumbnail.' . $path_parts['extension'];
            }

            $title = $item_data['title'];

            $query_owner_name = self::execute(
                "SELECT DISTINCT u.pseudo as owner_name FROM users u JOIN v_items_status i on i.owner = u.id WHERE i.owner = :owner_id",
                ["owner_id" => $item_data['owner']]
            );
            $data_owner_name = $query_owner_name->fetch();
            $owner_name = $data_owner_name['owner_name'] ?? '';

            $price = $item_data['buy_now_price'] ?? 0;
            $current_bid = $item_data['max_bid'];

            $is_auction = $item_data['is_auction'];

            $today = new DateTime(AppTime::get_current_datetime());
            $end_at = new DateTime($item_data['end_at']);
            $time_left_datetime = $end_at->diff($today);
            $time_left = $time_left_datetime->days . "d " . $time_left_datetime->h . "h";

            $user_bid_items[] = new Item($user_bid, null, $item_data['id'], $title, '', $owner_name, '', $main_image_url, $image_urls, $price, $item_data['starting_bid'], new DateTime(AppTime::get_current_datetime()), $end_at, 0, $current_bid, 0, true, $is_auction, false, $has_buy_now, $buy_now_reached, $time_left, '');
        }

        return $user_bid_items;
    }

    public static function has_title_duplicate(int $owner_id, string $title, ?int $exclude_item_id = null): bool {
        // Retrieve all items belonging to this owner
        $owner_items = self::get_items_by_owner($owner_id);

        foreach ($owner_items as $item) {
            // 1. Skip checking if it's the exact same item we are currently editing
            if ($exclude_item_id !== null && $item->get_id() === $exclude_item_id) {
                continue;
            }

            // 2. Compare the trimmed titles
            if (trim($item->get_title()) === trim($title)) {
                return true; // Duplicate found!
            }
        }

        return false; // No duplicates found
    }

    public static function get_user_bid(int $item_id, int $user_id): float|null {
        $query = self::execute(
            "SELECT MAX(amount) as bid
            FROM bids
            WHERE owner = :user_id
            AND item = :item_id",
            [
                "item_id" => $item_id,
                "user_id" => $user_id
            ]
        );

        $data = $query->fetch();
        if (!$data) {
            return 0;
        }

        return $data['bid'];
    }

    public static function get_user_available_items(int $id, ?string $search = null, ?int $category_id = null): array {
        $user_available_items = [];

        $sql = "SELECT vis.*
                FROM v_items_status vis
                JOIN users seller ON vis.owner = seller.id
                WHERE :today BETWEEN vis.created_at AND vis.end_at 
                AND vis.owner != :id              
                AND vis.buy_now_reached = 0       
                AND NOT EXISTS (                  
                    SELECT 1 FROM bids b WHERE b.item = vis.id AND b.owner = :id
                )";

        $params = [
            "today" => AppTime::get_current_datetime(),
            "id" => $id
        ];

        if ($search !== null && trim($search) !== '') {
            $sql .= " AND (
                        vis.title LIKE :search 
                        OR seller.pseudo LIKE :search
                        OR vis.description LIKE :search
                      )";
            $params['search'] = '%' . trim($search) . '%';
        }

        if ($category_id !== null) {
            $sql .= " AND vis.id in (SELECT item FROM item_categories WHERE category = :category_id)";
            $params['category_id'] = $category_id;
        }

        $sql .= " ORDER BY vis.end_at";

        $query = self::execute($sql, $params);
        $data = $query->fetchAll();

        if (!$data) {
            return [];
        }

        foreach ($data as $item_data) {
            $user_bid = 0;
            $is_auction = $item_data['is_auction'];
            $has_buy_now = $item_data['has_buy_now'] == 1 ? true : false;
            $buy_now_reached = $item_data['buy_now_reached'];

            $query_image_urls = self::execute(
                "SELECT picture_path FROM item_pictures p WHERE p.item = :item_id ORDER BY priority ASC",
                ["item_id" => $item_data['id']]
            );
            $image_urls = (array) $query_image_urls->fetchAll(PDO::FETCH_COLUMN);

            $main_image_url = "uploads/items/51/itemPlaceholder_thumbnail.png";
            if (!empty($image_urls)) {
                $main_image_url = $image_urls[0];
                $path_parts = pathinfo($main_image_url);
                $main_image_url = $path_parts['dirname'] . '/' . $path_parts['filename'] . '_thumbnail.' . $path_parts['extension'];
            }

            $title = $item_data['title'];

            $query_owner_name = self::execute(
                "SELECT DISTINCT u.pseudo as owner_name FROM users u JOIN v_items_status i on i.owner = u.id WHERE i.owner = :owner_id",
                ["owner_id" => $item_data['owner']]
            );
            $data_owner_name = $query_owner_name->fetch();
            $owner_name = $data_owner_name['owner_name'] ?? '';

            $price = $item_data['buy_now_price'] ?? 0;

            $is_auction = $item_data['is_auction'];

            if ($is_auction) {
                $current_bid = $item_data['has_bids'] ? $item_data['max_bid'] : $item_data['starting_bid'];
            } else {
                $current_bid = 0;
            }

            $today = new DateTime(AppTime::get_current_datetime());
            $end_at = new DateTime($item_data['end_at']);
            $time_left_datetime = $end_at->diff($today);
            $time_left = $time_left_datetime->days . "d " . $time_left_datetime->h . "h";

            $user_available_items[] = new Item($user_bid, null, $item_data['id'], $title, '', $owner_name, '', $main_image_url, $image_urls, $price, $item_data['starting_bid'], new DateTime(AppTime::get_current_datetime()), $end_at, 0, $current_bid, 0, true, $is_auction, false, $has_buy_now, $buy_now_reached, $time_left, '');
        }

        return $user_available_items;
    }

    public function save(): void {
        if ($this->id > 0) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    public function update(): void {
        $sql = "UPDATE items SET
        title = :title,
        description = :description,
        buy_now_price = :buy_now_price,
        duration_days = :duration_days,
        starting_bid = :starting_bid
        WHERE id = :id";
        self::execute($sql, [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "buy_now_price" => $this->buy_now_price,
            "duration_days" => $this->duration_days,
            "starting_bid" => $this->starting_bid
        ]);
    }

    private function insert() {
        $sql = "INSERT into items (
        title,
        description,
        owner,
        created_at,
        buy_now_price,
        duration_days,
        starting_bid
        ) Values (
        :title,
        :description,
        (SELECT id FROM users Where pseudo = :owner_name),
        :created_at,
        :buy_now_price,
        :duration_days,
        :starting_bid
        )";
        self::execute($sql, [
            "title" => $this->title,
            "description" => $this->description,
            "owner_name" => $this->owner_name,
            "created_at" => $this->created_at->format('Y-m-d H:i:s'),
            "buy_now_price" => $this->buy_now_price,
            "duration_days" => $this->duration_days,
            "starting_bid" => $this->starting_bid
        ]);

        $sql_id = "SELECT MAX(id) as id FROM items";
        $request = self::execute($sql_id, []);
        $result = $request->fetch();

        $newID = $result['id'];
        $this->id = $newID;
        return $newID;
    }

    public static function change_pics_left(int $image_id, int $item_id): void {
        $query = self::execute(
            "SELECT priority, picture_path
            FROM item_pictures
            WHERE item = :item_id
            ORDER BY priority ASC",
            ["item_id" => $item_id]
        );
        $pictures = $query->fetchAll();

        if ($image_id > 0 && isset($pictures[$image_id])) {

            $current_img = $pictures[$image_id];
            $previous_img = $pictures[$image_id - 1];

            $current_priority = $current_img['priority'];
            $previous_priority = $previous_img['priority'];

            self::execute(
                "UPDATE item_pictures SET priority = -1 WHERE item = :item AND priority = :current_priority",
                ["item" => $item_id, "current_priority" => $current_priority]
            );

            self::execute(
                "UPDATE item_pictures SET priority = :current_priority WHERE item = :item AND priority = :previous_priority",
                ["item" => $item_id, "previous_priority" => $previous_priority, "current_priority" => $current_priority]
            );

            self::execute(
                "UPDATE item_pictures SET priority = :previous_priority WHERE item = :item AND priority = -1",
                ["item" => $item_id, "previous_priority" => $previous_priority]
            );
        }
    }

    public static function change_pics_right(int $image_id, int $item_id): void {
        $query = self::execute(
            "SELECT priority, picture_path
            FROM item_pictures
            WHERE item = :item_id
            ORDER BY priority ASC",
            ["item_id" => $item_id]
        );
        $pictures = $query->fetchAll();

        if ($image_id < count($pictures) - 1 && isset($pictures[$image_id])) {

            $current_img = $pictures[$image_id];
            $next_img = $pictures[$image_id + 1];

            $current_priority = $current_img['priority'];
            $next_priority = $next_img['priority'];

            self::execute(
                "UPDATE item_pictures SET priority = -1 WHERE item = :item AND priority = :current_priority",
                ["item" => $item_id, "current_priority" => $current_priority]
            );

            self::execute(
                "UPDATE item_pictures SET priority = :current_priority WHERE item = :item AND priority = :next_priority",
                ["item" => $item_id, "next_priority" => $next_priority, "current_priority" => $current_priority]
            );

            self::execute(
                "UPDATE item_pictures SET priority = :next_priority WHERE item = :item AND priority = -1",
                ["item" => $item_id, "next_priority" => $next_priority]
            );
        }
    }

    public static function delete_images(string $filename, int $item_id): void {
        $query = self::execute(
            "SELECT priority, picture_path
            FROM item_pictures
            WHERE item = :item_id",
            ["item_id" => $item_id]
        );
        $pictures = $query->fetchAll();

        $image_to_delete = null;

        foreach ($pictures as $pic) {
            if (basename($pic['picture_path']) === $filename) {
                $image_to_delete = $pic;
                break;
            }
        }

        if ($image_to_delete) {

            $main_path = $image_to_delete['picture_path'];
            $thumb_path = str_replace(".jpg", "_thumbnail.jpg", $main_path);

            if (file_exists($main_path)) {
                unlink($main_path);
            }

            if (file_exists($thumb_path)) {
                unlink($thumb_path);
            }

            self::execute(
                "DELETE FROM item_pictures WHERE item = :item_id AND priority = :priority",
                [
                    "item_id" => $item_id,
                    "priority" => $image_to_delete['priority']
                ]
            );
            self::execute(
                "UPDATE item_pictures SET priority = priority - 1 WHERE item = :item_id AND priority > :deleted_priority",
                [
                    "item_id" => $item_id,
                    "deleted_priority" => $image_to_delete['priority']
                ]
            );
        }
    }

    public static function add_picture(int $item_id, string $path): void {
        $query = self::execute(
            "SELECT MAX(priority) as max_p FROM item_pictures WHERE item = :item_id",
            ["item_id" => $item_id]
        );
        $data = $query->fetch();
        $next_priority = ($data['max_p']) ? $data['max_p'] + 1 : 1;

        self::execute(
            "INSERT INTO item_pictures (item, picture_path, priority) VALUES (:item, :path, :priority)",
            [
                "item" => $item_id,
                "path" => $path,
                "priority" => $next_priority
            ]
        );
    }

    public static function add_categories(int $item_id, int $category_id): void {
        self::execute(
            "INSERT INTO item_categories (item, category) VALUES (:item_id, :category_id)",
            ["item_id" => $item_id, "category_id" => $category_id]
        );
    }

    public function remove_categories(int $item_id): void {
        self::execute(
            "DELETE FROM item_categories WHERE item = :item_id",
            ["item_id" => $item_id]
        );
    }

    public static function get_purchases(int $id): array {
        $purchases = [];

        $query = self::execute(
            "SELECT vis.*
            FROM v_items_status vis
            JOIN bids b ON b.item = vis.id          
            JOIN users winner ON winner.id = b.owner 
            WHERE b.owner = :id                      
            AND b.amount = vis.max_bid            -- Best bidder / Buyer
            AND (vis.buy_now_reached = 1 OR :today >= vis.end_at) -- Item not expired yet or not sold yet
            ORDER BY vis.end_at",
            [
                "today" => AppTime::get_current_datetime(),
                "id" => $id
            ]
        );

        $data = $query->fetchAll();
        if (!$data) {
            return [];
        }

        foreach ($data as $item_data) {
            // available items have no user bid on them
            $user_bid = Item::get_user_bid($item_data['id'], $id); // for debug, for is boris id
            if (is_null($user_bid)) {
                $user_bid = 0;
            }

            // we get info of whether the item is in auction or not
            $is_auction = $item_data['is_auction'];

            // we get the info whether or not the item can be purchased directly
            $has_buy_now = $item_data['has_buy_now'];
            $has_buy_now = $has_buy_now == 1 ? true : false;

            // we get the info whether or not the item last bid exceeds the direct sale price
            $buy_now_reached = $item_data['buy_now_reached'];

            // we retrieve item pictures
            $query_image_urls = self::execute(
                "SELECT picture_path
                                FROM item_pictures p
                                where p.item = :item_id
                                Order BY priority ASC",
                ["item_id" => $item_data['id']]
            );

            $data_image_urls = $query_image_urls->fetchAll(PDO::FETCH_COLUMN);

            $image_urls = (array) $data_image_urls;

            // Image par défaut (Placeholder)
            $main_image_url = "uploads/items/51/itemPlaceholder_thumbnail.png";

            // we retrieve main picture
            if (!empty($image_urls)) {
                $main_image_url = $image_urls[0];
                $path_parts = pathinfo($main_image_url);
                $main_image_url = $path_parts['dirname'] . '/' .
                    $path_parts['filename'] .
                    '_thumbnail.' .
                    $path_parts['extension'];
            }

            // we retrieve the title
            $title = $item_data['title'];

            // we retrieve owner name
            $query_owner_name = self::execute(
                "SELECT DISTINCT u.pseudo as owner_name
                                from users u
                                join v_items_status i on i.owner = u.id
                                where i.owner = :owner_id",
                ["owner_id" => $item_data['owner']]
            );

            $data_owner_name = $query_owner_name->fetch();

            if (!$data) {
                return [];
            }

            $owner_name = $data_owner_name['owner_name'];

            // we retrieve the price
            $price = $item_data['buy_now_price'];
            if ($price == null) {
                $price = 0;
            }


            // the max bid by default is the user bid because he's the one who purchased it 
            $current_bid = $user_bid;

            // unused variables
            $description = '';
            $owner_role = '';
            $duration_days = 0;


            // By default, query return only "Closed" items
            $time_left = "Closed";

            // for debug
            $today = new DateTime(AppTime::get_current_datetime());
            $end_at =  new DateTime($item_data['end_at']);
            $time_left_datetime = $end_at->diff($today);
            $time_left_debug = $time_left_datetime->invert;

            $purchases[] = new Item($user_bid, null, $item_data['id'], $title, $description, $owner_name, $owner_role, $main_image_url, $image_urls, $price, $current_bid, new DateTime(AppTime::get_current_datetime()), $end_at, $duration_days, $current_bid, 0, true, $is_auction, false, $has_buy_now, $buy_now_reached, $time_left, '');
        }

        return $purchases;
    }

    public static function get_sales(int $user_id): array {
        $sales = [];

        $query = self::execute(
            "SELECT vis.*, u.pseudo as bidder_name
            FROM v_items_status vis
            JOIN bids b ON b.item = vis.id
            JOIN users u ON u.id = b.owner

            WHERE vis.owner = :id       -- Seller
            AND b.amount = vis.max_bid -- Highest bid put on the seller item
            AND (vis.buy_now_reached = 1 OR :today >= vis.end_at) -- Item not expired yet or not sold yet
            ORDER BY vis.end_at",
            [
                "today" => AppTime::get_current_datetime(),
                "id" => $user_id
            ]
        );

        $data = $query->fetchAll();
        if (!$data) {
            return [];
        }

        foreach ($data as $item_data) {
            // should be 0/null because user is the owner
            $user_bid = Item::get_user_bid($item_data['id'], $user_id);
            if (is_null($user_bid)) {
                $user_bid = 0;
            }

            // we retrieve name of bidder
            $bidder_name = $item_data['bidder_name'];

            // we get info of whether the item is in auction or not
            $is_auction = $item_data['is_auction'];

            // we get the info whether or not the item can be purchased directly
            $has_buy_now = $item_data['has_buy_now'];
            $has_buy_now = $has_buy_now == 1 ? true : false;

            // we get the info whether or not the item last bid exceeds the direct sale price
            $buy_now_reached = $item_data['buy_now_reached'];

            // we retrieve item pictures
            $query_image_urls = self::execute(
                "SELECT picture_path
                                FROM item_pictures p
                                where p.item = :item_id
                                Order BY priority ASC",
                ["item_id" => $item_data['id']]
            );

            $data_image_urls = $query_image_urls->fetchAll(PDO::FETCH_COLUMN);

            $image_urls = (array) $data_image_urls;

            // Image par défaut (Placeholder)
            $main_image_url = "uploads/items/51/itemPlaceholder_thumbnail.png";

            // we retrieve main picture
            if (!empty($image_urls)) {
                $main_image_url = $image_urls[0];
                $path_parts = pathinfo($main_image_url);
                $main_image_url = $path_parts['dirname'] . '/' .
                    $path_parts['filename'] .
                    '_thumbnail.' .
                    $path_parts['extension'];
            }

            // we retrieve the title
            $title = $item_data['title'];

            // we retrieve owner name (in this case, it should be always the user because the item is one of its sales)
            $query_owner_name = self::execute(
                "SELECT DISTINCT u.pseudo as owner_name
                                from users u
                                join v_items_status i on i.owner = u.id
                                where i.owner = :owner_id",
                ["owner_id" => $item_data['owner']]
            );

            $data_owner_name = $query_owner_name->fetch();

            if (!$data) {
                return [];
            }

            $owner_name = $data_owner_name['owner_name'];

            // we retrieve the price
            $price = $item_data['buy_now_price'];
            if ($price == null) {
                $price = 0;
            }


            // the max bid
            $current_bid = $item_data['max_bid'];

            // unused variables
            $description = '';
            $owner_role = '';
            $duration_days = 0;


            // By default, query return only "Closed" items
            $time_left = "Closed";

            // for debug
            $today = new DateTime(AppTime::get_current_datetime());
            $end_at =  new DateTime($item_data['end_at']);
            $time_left_datetime = $end_at->diff($today);
            $time_left_debug = $time_left_datetime->invert;

            $sales[] = new Item($user_bid, $bidder_name, $item_data['id'], $title, $description, $owner_name, $owner_role, $main_image_url, $image_urls, $price, $current_bid, new DateTime(AppTime::get_current_datetime()), $end_at, $duration_days, $current_bid, 0, true, $is_auction, false, $has_buy_now, $buy_now_reached, $time_left, '');
        }

        return $sales;
    }

    public static function get_all_available_items(?int $category_id = null): array {
        $items = [];
        $sql = "SELECT * from v_items_status vis
                WHERE :today BETWEEN vis.created_at AND vis.end_at -- Not expired yet
                AND vis.buy_now_reached = 0 -- Not sold yet";
        $params = [
            "today" => AppTime::get_current_datetime()
        ];

        if ($category_id !== null) {
            $sql .= " AND id in (SELECT item FROM item_categories WHERE category = :category_id)";
            $params['category_id'] = $category_id;
        }
        $sql .= " ORDER by vis.end_at";
        $query = self::execute($sql, $params);
        $data = $query->fetchAll();
        if (!$data) {
            return [];
        }

        foreach ($data as $item_data) {
            // available items have no user bid on them
            $user_bid = 0;

            // we get info of whether the item is in auction or not
            $is_auction = $item_data['is_auction'];

            // we get the info whether or not the item can be purchased directly
            $has_buy_now = $item_data['has_buy_now'];
            $has_buy_now = $has_buy_now == 1 ? true : false;

            // we get the info whether or not the item last bid exceeds the direct sale price
            $buy_now_reached = $item_data['buy_now_reached'];

            // we retrieve item pictures
            $query_image_urls = self::execute(
                "SELECT picture_path
                FROM item_pictures p
                where p.item = :item_id
                Order BY priority ASC",
                ["item_id" => $item_data['id']]
            );

            $data_image_urls = $query_image_urls->fetchAll(PDO::FETCH_COLUMN);

            $image_urls = (array) $data_image_urls;

            // Image par défaut (Placeholder)
            $main_image_url = "uploads/items/51/itemPlaceholder_thumbnail.png";

            // we retrieve main picture
            if (!empty($image_urls)) {
                $main_image_url = $image_urls[0];
                $path_parts = pathinfo($main_image_url);
                $main_image_url = $path_parts['dirname'] . '/' .
                    $path_parts['filename'] .
                    '_thumbnail.' .
                    $path_parts['extension'];
            }

            // we retrieve the title
            $title = $item_data['title'];

            // we retrieve owner name
            $query_owner_name = self::execute(
                "SELECT DISTINCT u.pseudo as owner_name
                from users u
                join v_items_status i on i.owner = u.id
                where i.owner = :owner_id",
                ["owner_id" => $item_data['owner']]
            );

            $data_owner_name = $query_owner_name->fetch();

            if (!$data) {
                return [];
            }

            $owner_name = $data_owner_name['owner_name'];

            // we retrieve the price
            $price = $item_data['buy_now_price'];
            if ($price == null) {
                $price = 0;
            }


            // we retrieve the current bid -> max bid for this item
            $has_bids = $item_data['has_bids'];
            if ($is_auction) {
                if ($has_bids) {
                    $current_bid = $item_data['max_bid'];
                } else {
                    $current_bid =  $item_data['starting_bid'];
                }
            } else {
                $current_bid = 0;
            }

            // unused variables
            $description = '';
            $owner_role = '';
            $duration_days = 0;

            // we retrieve time left
            $today = new DateTime(AppTime::get_current_datetime());
            $end_at = new DateTime($item_data['end_at']);
            $time_left_datetime = $end_at->diff($today);
            $time_left = $time_left_datetime->days . "d " . $time_left_datetime->h . "h";

            $items[] = new Item($user_bid, null, $item_data['id'], $title, $description, $owner_name, $owner_role, $main_image_url, $image_urls, $price, 0, new DateTime(AppTime::get_current_datetime()), $end_at, $duration_days, $current_bid, 0, true, $is_auction, false, $has_buy_now, $buy_now_reached, $time_left, '');
        }

        return $items;
    }

    public static function update_image_priority(int $item_id, array $order): void {
        foreach ($order as $priority => $filename) { // on set toutes les priorités en négatifs pour éviter les chevauchements
            self::execute(
                "UPDATE item_pictures SET priority = :priority
                WHERE item = :item_id AND picture_path LIKE :filename",
                [
                    "item_id" => $item_id,
                    "priority" => - ($priority + 1),
                    "filename" => "%" . $filename
                ]
            );
        }

        foreach ($order as $priority => $filename) { // on set les bonne priorités
            self::execute(
                "UPDATE item_pictures SET priority = :priority
                WHERE item = :item_id AND picture_path LIKE :filename",
                [
                    "item_id" => $item_id,
                    "priority" => $priority + 1,
                    "filename" => "%" . $filename
                ]
            );
        }
    }

    public function validate_before_fill(array $config): array {
        $errors = [];
        $min_lenght = (int)$config['min_item_lenght'];
        $max_lenght = (int)$config['max_item_lenght'];
        $min_duration = (int)$config['min_duration_days'];
        $max_duration = (int)$config['max_duration_days'];



        if (strlen($this->get_title()) == 0 || empty($this->get_title())) {
            $errors['empty'] = "Title is required";
        }

        if (strlen($this->get_title()) < $min_lenght || strlen($this->get_title()) > $max_lenght) {

            $errors['title'] = "Title length must be between $min_lenght and $max_lenght characters.";
        }

        if ($this->get_duration_days() < $min_duration || $this->get_duration_days() > $max_duration) {
            $errors['duration'] = "Duration must be at least $min_duration day and at most $max_duration days";
        }

        // vérification si l'utilisateur n'a pas un autre item du même nom dans ses items
        // on récupère l'id du propriétaire de l'item
        $query_owner_id = self::execute(
            "SELECT u.id FROM users u WHERE u.pseudo = :pseudo",
            ["pseudo" => $this->get_owner_name()]
        );
        $data = $query_owner_id->fetch();
        $owner_id = $data ? (int)$data['id'] : null;

        // on verifie si le propriétaire a un item avec le même titre
        $is_duplicate = $this->has_title_duplicate($owner_id, $this->get_title(), $this->get_id());
        if ($is_duplicate) {
            $errors['duplicate_title'] = "Title should be unique among user items.";
        }

        $starting_bid = $this->get_starting_bid();
        $buy_now_price = $this->get_buy_now_price();
        $is_direct_sale = $this->get_is_direct_sale();

        if ($starting_bid !== null && $starting_bid > 0) {
            if ($buy_now_price !== null && $buy_now_price > 0 && $buy_now_price < $starting_bid) {
                $errors['buy_now_price'] = "Instant purchase price must be higher than starting bid price";
            }
            if ($is_direct_sale) {
                $errors['sale_price'] = "You can't put a direct sale price and a starting bid.";
            }
        } elseif (!($is_direct_sale && $buy_now_price !== null && $buy_now_price > 0)) {
            $errors['sale'] = "You must either fill a starting bid or a direct sale price.";
        }

        return $errors;
    }
}
