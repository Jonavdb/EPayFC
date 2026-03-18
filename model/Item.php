<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Item extends Model
{

    public function __construct(
        public float $user_bid,
        public ?string $bidder_name,
        public int $id,
        public string $title,
        public string $description,
        public string $owner_name,
        public string $owner_role,
        public string $main_image_url,
        public array $image_urls,
        public ?float $buy_now_price,
        public ?float $starting_bid,
        public DateTime $created_at,
        public DateTime $end_at,
        public int $duration_days,
        public ?float $current_bid,
        public int $bid_count,
        public bool $has_bids,
        public bool $is_auction,
        public bool $is_direct_sale,
        public bool $has_buy_now,
        public bool $buy_now_reached,
        public string $time_left,
        public string $owner_picture
    ) {}

    public static function get_items_by_owner(int $owner_id): array
    {
        // Récupère les items du propriétaire, triés par date de fin calculée
        $sql = "SELECT * FROM v_items_status WHERE owner = :owner ORDER BY end_at ASC";
        $query = self::execute($sql, ["owner" => $owner_id]);
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

            // On récupère un tableau simple de chaînes (chemins)
            $image_urls = $query_image_urls->fetchAll(PDO::FETCH_COLUMN);

            // Image par défaut (Placeholder)
            $main_image_url = "css/images/default_item_image.png";

            // Si on a trouvé des images en base de données
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

    public static function get_item_by_id(int $id)
    {
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

    // Delete item
    public static function delete(int $id)
    {
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

    public static function get_user_bid_items(int $id)
    {
        $user_bid_items = [];

        $query = self::execute(
            // Active Auctions bid on by user
            "SELECT DISTINCT vis.*
            from users u
            join bids b on b.owner = u.id
            join v_items_status vis on vis.id = b.item
            where u.id = :id
            and :today BETWEEN vis.created_at AND vis.end_at
            and vis.buy_now_reached = 0
            order by vis.end_at",
            [
                "id" => $id,
                "today" => AppTime::get_current_datetime()
            ]
        );

        $data = $query->fetchAll();

        if (!$data) {
            return [];
        }

        foreach ($data as $item_data) {

            // we retrieve user bid on the item
            $user_bid = Item::get_user_bid($item_data['id'], $id);
            if (is_null($user_bid)) {
                $user_bid = 0;
            }

            // items on which user has placed bids are always in auction
            $is_auction = true;

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
            $main_image_url = "";

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
                return false;
            }

            $owner_name = $data_owner_name['owner_name'];

            // we retrieve the price
            $price = $item_data['buy_now_price'];
            if ($price == null) {
                $price = 0;
            }

            // we retrieve the current bid -> max bid for this item
            $current_bid = $item_data['max_bid'];

            // we retrive time left
            $today = new DateTime(AppTime::get_current_datetime());
            $end_at = new DateTime($item_data['end_at']);
            $time_left_datetime = $end_at->diff($today);
            $time_left = $time_left_datetime->days . "d " . $time_left_datetime->h . "h";

            // unused variables
            $description = '';
            $owner_role = '';
            $duration_days = 0;

            $user_bid_items[] = new Item($user_bid, null, $item_data['id'], $title, $description, $owner_name, $owner_role, $main_image_url, $image_urls, $price, $item_data['starting_bid'], new DateTime(AppTime::get_current_datetime()), $end_at, $duration_days, $current_bid, 0, true, false, false, $has_buy_now, $buy_now_reached, $time_left, '');
        }

        return $user_bid_items;
    }

    public static function get_user_bid(int $item_id, int $user_id)
    {
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

    public static function get_user_available_items(int $id)
    {
        $user_available_items = [];
        $query = self::execute(
            "SELECT vis.*
            FROM v_items_status vis
            WHERE :today BETWEEN vis.created_at AND vis.end_at -- Not expired yet
            AND vis.owner != :id              -- Not my item
            AND vis.buy_now_reached = 0       -- Not sold yet (Auction or Direct)
            AND NOT EXISTS (                  -- I haven't bid on it
                SELECT 1 
                FROM bids b 
                WHERE b.item = vis.id 
                AND b.owner = :id
            )
            ORDER by vis.end_at",
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
                return false;
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

            $user_available_items[] = new Item($user_bid, null, $item_data['id'], $title, $description, $owner_name, $owner_role, $main_image_url, $image_urls, $price, $item_data['starting_bid'], new DateTime(AppTime::get_current_datetime()), $end_at, $duration_days, $current_bid, 0, true, $is_auction, false, $has_buy_now, $buy_now_reached, $time_left, '');
        }

        return $user_available_items;
    }

    public function save()
    {
        if ($this->id > 0) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    public function update()
    {
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

    private function insert()
    {
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

    public static function change_pics_left(int $image_id, int $item_id)
    {
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

    public static function change_pics_right(int $image_id, int $item_id)
    {
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

    public static function delete_images(string $filename, int $item_id)
    {
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

    public static function add_picture(int $item_id, string $path)
    {

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

    public static function get_purchases(int $id)
    {
        $purchases = [];

        // BACKUP
        // $query = self::execute(
        //     // query to get the items where the specified user got the winning 
        //     // OR the items that he purchased directly
        //     "SELECT 
        //     /* Item Data */
        //     vis.id AS item_id,
        //     vis.title,
        //     vis.buy_now_price,
        //     vis.is_auction,
        //     vis.has_buy_now,
        //     vis.buy_now_reached,
        //     vis.end_at,
        //     vis.max_bid,

        //     /* Seller Data (The person who owns the item) */
        //     seller.id AS seller_id,         -- id of the person who owns the item (seller is the custom name of users table ie check next query lines)
        //     seller.pseudo AS seller_name,   -- name of the person who owns the item

        //     /* Bidder Data (The person who placed the winning bid) */
        //     u.id AS bidder_id,
        //     u.pseudo AS bidder_name,        -- name of the person of placed the bid 
        //     b.amount AS winning_bid_amount  -- bid placed by the person 

        //     FROM v_items_status vis
        //     /* Join for the bid details */
        //     JOIN bids b ON b.item = vis.id

        //     /* Join for the person who placed the bid (Bidder / Buyer) */
        //     JOIN users u ON u.id = b.owner

        //     /* Join for the person who created the item (Seller) */
        //     JOIN users seller ON seller.id = vis.owner 

        //     WHERE u.id = :id             -- buyer / bidder
        //     AND b.amount = vis.max_bid   -- with the highest bid    
        //     AND (vis.buy_now_reached = 1 OR :today >= vis.end_at)  -- who bought the item or date expired

        //     ORDER BY vis.end_at",
        //     [
        //         "today" => AppTime::get_current_datetime(),
        //         "id" => $id
        //     ]
        // );

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
                return false;
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

    public static function get_sales(int $user_id)
    {

        $sales = [];

        // BACKUP
        // $query = self::execute(
        // // query to get the sales of the specified user
        // "SELECT 
        // /* Item Data */
        // vis.id AS item_id,
        // vis.title,
        // vis.buy_now_price,
        // vis.is_auction,
        // vis.has_buy_now,
        // vis.buy_now_reached,
        // vis.end_at,
        // vis.max_bid,

        // /* Seller Data (The person who owns the item) */
        // seller.id AS seller_id,         -- id of the person who owns the item (seller is the custom name of users table ie check next query lines)
        // seller.pseudo AS seller_name,   -- name of the person who owns the item

        // /* Bidder Data (The person who placed the winning bid) */
        // u.id AS bidder_id,
        // u.pseudo AS bidder_name,        -- name of the person of placed the bid 
        // b.amount AS winning_bid_amount  -- bid placed by the person 

        // FROM v_items_status vis
        // /* Join for the bid details */
        // JOIN bids b ON b.item = vis.id

        // /* Join for the person who placed the bid (Bidder / Buyer) */
        // JOIN users u ON u.id = b.owner

        // /* Join for the person who created the item (Seller) */
        // JOIN users seller ON seller.id = vis.owner 

        // WHERE seller.id = :user_id        -- seller 
        // AND b.amount = vis.max_bid       -- with the highest bid instance on the item    
        // AND (vis.buy_now_reached = 1 OR :today >= vis.end_at)  -- who bought the item or date expired
        // ORDER BY vis.end_at",
        // [
        //     "today" => AppTime::get_current_datetime(),
        //     "user_id" => $user_id
        // ]
        //);

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
                return false;
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

    public static function get_all_available_items()
    {
        $items = [];
        $query = self::execute(
            "SELECT * from v_items_status vis
            WHERE :today BETWEEN vis.created_at AND vis.end_at -- Npt expired yet
            AND vis.buy_now_reached = 0 -- Not sold yet
            ORDER by vis.end_at",
            [
                "today" => AppTime::get_current_datetime(),
            ]
        );

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
                return false;
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
}
