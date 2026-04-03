<?php

require_once "framework/Controller.php";
require_once "model/User.php";
require_once "model/Item.php";
require_once "model/Bid.php";
require_once "utils/AppTime.php";
require_once "utils/ImageTool.php";

class ControllerItem extends Controller {
    public function index(): void {
        $this->redirect("item", "list");
    }

    // My items
    public function list(): void {
        $user = $this->get_user_or_redirect();
        $items = Item::get_items_by_owner($user->get_id());

        $active_items = [];
        $sold_items = [];
        $unsold_items = [];
        $now = new DateTime();

        foreach ($items as $item) {
            // Préparation des données d'affichage (photos des items, etc...)
            $display_data = $this->prepare_item_data($item, $now);

            // Tri dans les catégories
            $is_buy_now = $item->get_buy_now_reached();
            $is_expired = $item->get_end_at() <= $now;
            $has_bids   = $item->get_bid_count() > 0;

            // CAS 1 : VENDU (Achat immédiat OU Temps écoulé avec enchères)
            if ($is_buy_now || ($is_expired && $has_bids)) {
                $display_data->context = 'sold';
                $sold_items[] = $display_data;
            } elseif ($is_expired && !$has_bids) {
                // CAS 2 : NON VENDU (Temps écoulé sans enchères)
                $display_data->context = 'unsold';
                $unsold_items[] = $display_data;
            } else {
                // CAS 3 : ACTIF (Ni fini, ni acheté)
                $display_data->context = 'active';
                $active_items[] = $display_data;
            }
        }

        (new View("my_items"))->show([
            "user" => $user,
            "active_items" => $active_items,
            "sold_items" => $sold_items,
            "unsold_items" => $unsold_items
        ]);
    }

    /* Helper pour My Items : Prépare les données pour l'affichage */
    private function prepare_item_data(Item $item, DateTime $now): object {
        $is_closed = $item->get_end_at() <= $now;

        // Calcul du texte temps
        if ($is_closed) {
            $time_left_str = "Closed";
        } else {
            $diff = $now->diff($item->get_end_at());
            $time_left_str = ($diff->days > 0) ? $diff->days . " days left" : $diff->h . "h " . $diff->i . "m left";
        }

        // Image
        $img_src = !empty($item->get_main_image_url()) ? $item->get_main_image_url() : "uploads/items/item_placeholder.png";

        // Compteur images
        $total_image_count = is_array($item->get_image_urls()) ? count($item->get_image_urls()) : 0;
        $extra_image_count = max(0, $total_image_count - 1);

        // On retourne un objet générique
        return (object) [
            'raw_item' => $item,
            'time_left' => $time_left_str,
            'img_src' => $img_src,
            'extra_images' => $extra_image_count,
            'context' => ''               // Sera rempli dans la boucle list()
        ];
    }

    // Delete item
    public function delete($id = null): void {
        if ($id === null && isset($_GET['param1'])) {
            $id = $_GET['param1'];
        }

        if (!$id) {
            $this->redirect("item", "list");
            return;
        }

        $user = $this->get_user_or_redirect();
        $item = Item::get_item_by_id($id);

        // Vérification : L'item existe
        if (!$item) {
            $this->redirect("item", "list");
            return;
        }

        // Vérification (seulement l'user connecté est le propriétaire)
        if ($user->get_pseudo() != $item->get_owner_name()) {
            $this->redirect("item", "list");
            return;
        }

        // Suppression
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            Item::delete($id);
            $this->redirect("item", "list");
        } else {
            (new View("delete_item"))->show([
                "item" => $item,
                "user" => $user
            ]);
        }
    }

    // Open item
    public function open(): void {
        // On regarde si le premier argument (l'ID) existe
        if (isset($_GET['param1'])) {
            $id = (int)$_GET['param1']; // On récupère le 8 de l'URL ".../index/8"
        } else {
            $this->redirect('item', 'browse');
            return;
        }
        $item = Item::get_item_by_id($id);
        if (empty($item)) {
            $this->redirect('item', 'browse');
            return;
        }
        $image_index = isset($_GET['param2']) ? (int)$_GET['param2'] : 0;
        $image_urls = $item->get_image_urls();
        if (isset($image_urls[$image_index])) {
            $item->set_main_image_url($image_urls[$image_index]);
        }

        $bids = Bid::get_bid_by_item_id($id);
        $user = $this->get_user_or_false();
        $current_user_pseudo = $user ? $user->get_pseudo() : null;


        (new View("open_item"))->show([
            "item" => $item,
            "bids" => $bids,
            "current_user" => $current_user_pseudo,
            'selected_image' => $image_index
        ]);
    }

    public function new_bid() {

        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0.0;

        $user = $this->get_user_or_redirect();
        $current_user_pseudo = $user->get_pseudo();

        $item = Item::get_item_by_id($item_id);
        $now = AppTime::get_current_datetime();

        if (!$item) {
            $this->redirect('item', 'browse');
            return;
        }

        if ($item->get_owner_name() === $current_user_pseudo) {
            $this->redirect('item', 'list');
            return;
        }

        if ($item->get_end_at() < $now) {
            $this->redirect('item', 'open', $item_id);
            return;
        }

        if ($item_id > 0 && $amount > 0) {
            Bid::create_bid($current_user_pseudo, $item_id, $amount);
        }

        $this->redirect("item", "open", $item_id);
    }

    public function buy_now() {
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $user = $this->get_user_or_redirect();
        $current_user_pseudo = $user->get_pseudo();
        if ($item_id > 0) {

            $item = Item::get_item_by_id($item_id);

            if ($item && $item->get_owner_name() !== $current_user_pseudo && $item->get_has_buy_now() && !$item->get_buy_now_reached()) {
                Bid::create_bid($current_user_pseudo, $item_id, $item->get_buy_now_price());
            }
        }
        $this->redirect("item", "open", $item_id);
    }


    public function add() {
        $user = $this->get_user_or_redirect();
        $now = new DateTime(AppTime::get_current_datetime());
        $end = (clone $now)->modify('+7 days');
        $errors = [];

        $item = new Item(
            0.0,
            null,
            0,
            "",
            "",
            $user->get_pseudo(),
            "",
            "",
            [],
            0.0,
            0.0,
            $now,
            $end,
            7,
            0.0,
            0,
            false,
            false,
            false,
            false,
            false,
            "",
            ""
        );

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $errors = $this->validate_and_fill($item);

            if (empty($errors)) {
                $item->save();
                $this->redirect("item", "open", $item->get_id());
            }
        }

        (new View("add_edit_item"))->show([
            "item" => $item,
            "errors" => $errors
        ]);
    }

    private function validate_and_fill(Item $item): array {

        $config_path = "config/dev.ini";
        $config = parse_ini_file($config_path);
        $min_lenght = (int)$config['min_item_lenght'];
        $max_lenght = (int)$config['max_item_lenght'];
        $min_duration = (int)$config['min_duration_days'];
        $max_duration = (int)$config['max_duration_days'];
        $item->set_title(trim($_POST['title']));
        $item->set_description(trim($_POST['description']));
        $item->set_duration_days((int)$_POST['duration']);
        $errors = [];

        $starting_bid = ($_POST['starting_bid'] !== '') ? $_POST['starting_bid'] : null;
        $buy_now_price = ($_POST['instant'] !== '') ? $_POST['instant'] : null;
        $sale_price = ($_POST['direct'] !== '') ? $_POST['direct'] : null;

        if (strlen($item->get_title()) === 0 || empty($item->get_title())) {
            $errors['empty'] = "Title is required";
        }

        if (strlen($item->get_title()) < $min_lenght || strlen($item->get_title()) > $max_lenght) {
            $errors['title'] = "Title lenght must be between $min_lenght and $max_lenght characters.";
        }

        if ($item->get_duration_days() < $min_duration || $item->get_duration_days() > $max_duration) {
            $errors['duration'] = "Duration must be at least $min_duration day and at most $max_duration days";
        }

        if ($starting_bid !== null && $starting_bid > 0) {
            $item->set_starting_bid($starting_bid);
            $item->set_is_auction(true);
            $item->set_is_direct_sale(false);

            if ($buy_now_price !== null && $buy_now_price > 0) {
                if ($buy_now_price < $starting_bid) {
                    $errors['buy_now_price'] = "Instant purchase price must be higher than starting bid price";
                }
                $item->set_buy_now_price($buy_now_price);
                $item->set_has_buy_now(true);
            } else {
                $item->set_buy_now_price(null);
                $item->set_has_buy_now(false);
            }

            if ($sale_price !== null && $sale_price > 0) {
                $errors['sale_price'] = "You can't put a direct sale price and a starting bid.";
            }
        } elseif ($sale_price !== null && $sale_price > 0) {
            $item->set_buy_now_price($sale_price);
            $item->set_is_auction(false);
            $item->set_is_direct_sale(true);
            $item->set_has_buy_now(false);
            $item->set_starting_bid(0.0);
        } else {
            $errors['sale'] = "You must either fill a starting bid or a direct sale price.";
        }
        return $errors;
    }

    public function edit() {

        $user = $this->get_user_or_redirect();
        $id = $_GET['param1'] ?? $_POST['id'] ?? null;
        $errors = [];


        if (!$id) {
            $this->redirect("item", "index");
            return;
        }

        $item = Item::get_item_by_id($id);

        if ($item) {
            if ($item->get_owner_name() !== $user->get_pseudo() || ($item->get_is_auction() && $item->get_has_bids())) {
                $this->redirect("item", "open", $item->get_id());
                exit();
            }
        } else {
            $this->redirect("item", "index");
            return;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $errors = $this->validate_and_fill($item);

            if (empty($errors)) {
                $item->save();
                $this->redirect("item", "open", $item->get_id());
            }
        }

        (new View("add_edit_item"))->show([
            "item" => $item,
            "errors" => $errors
        ]);
    }



    public function manage_images(): void {

        if (isset($_GET['param1'])) {
            $id = (int)$_GET['param1'];
        } else {
            $this->redirect("item", "browse");
            return;
        }

        $item = Item::get_item_by_id($id);

        $user = $this->get_user_or_redirect();
        $current_user = $user->get_pseudo();

        if (!$item) {
            $this->redirect("item", "browse");
            return;
        }

        if ($item->get_owner_name() !== $current_user || ($item->get_is_auction() && $item->get_has_bids())) {
            $this->redirect("item", "open", $id);
            return;
        }

        (new View("manage_images"))->show([
            "item" => $item
        ]);
    }

    public function move_image_left() {
        $image_index = isset($_POST['image_index']) ? (int)$_POST['image_index'] : 0;
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $user = $this->get_user_or_redirect();
        $item = Item::get_item_by_id($item_id);
        if (!$item || $item->get_owner_name() !== $user->get_pseudo()) {
            $this->redirect('item', 'open', $item_id);
            return;
        }
        if ($item_id > 0) {
            Item::change_pics_left($image_index, $item_id);
        }
        $this->redirect("item", "manage_images", $item_id);
    }

    public function move_image_right() {
        $image_index = isset($_POST['image_index']) ? (int)$_POST['image_index'] : 0;
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $user = $this->get_user_or_redirect();
        $item = Item::get_item_by_id($item_id);
        if (!$item || $item->get_owner_name() !== $user->get_pseudo()) {
            $this->redirect('item', 'open', $item_id);
            return;
        }
        if ($item_id > 0) {
            Item::change_pics_right($image_index, $item_id);
        }
        $this->redirect("item", "manage_images", $item_id);
    }

    public function delete_images() {
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $filename = isset($_POST['image_filename']) ? $_POST['image_filename'] : '';
        $user = $this->get_user_or_redirect();
        $item = Item::get_item_by_id($item_id);
        if (!$item || $item->get_owner_name() !== $user->get_pseudo()) {
            $this->redirect('item', 'open', $item_id);
            return;
        }
        if ($item_id > 0 && !empty($filename)) {
            Item::delete_images($filename, $item_id);
        }
        $this->redirect("item", "manage_images", $item_id);
    }

    public function add_images() {
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $config_path = "config/dev.ini";
        $config = parse_ini_file($config_path);
        $max_size = (int)$config['upload_max_size'];
        $allowed_type = $config['upload_allowed_types'];
        $errors = [];
        $user = $this->get_user_or_redirect();
        $item = Item::get_item_by_id($item_id);
        if (!$item || $item->get_owner_name() !== $user->get_pseudo()) {
            $this->redirect('item', 'open', $item_id);
            return;
        }


        if ($item_id > 0 && isset($_FILES['image_files'])) {
            $files = $_FILES['image_files'];
            $count = count($files['name']);

            for ($i = 0; $i < $count; $i++) {

                $tmp_name = $files['tmp_name'][$i];
                $name     = $files['name'][$i];
                $size     = $files['size'][$i];
                $error    = $files['error'][$i];

                $file_valid = true;

                if ($error === 0) {

                    if ($size > $max_size) {
                        $errors[] = "The file $name is too big";
                        $file_valid = false;
                    }

                    $image_info = getimagesize($tmp_name);
                    if (!$image_info || !in_array($image_info['mime'], $allowed_type)) {
                        $errors[] = "Bad format for the file $name";
                        $file_valid = false;
                    }

                    if ($file_valid) {
                        $file = [
                            'name' => $files['name'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'size'     => $files['size'][$i],
                            'error'    => $files['error'][$i]
                        ];

                        $path = ImageTools::upload_and_resize($file, 'items', $item_id);

                        if ($path) {
                            Item::add_picture($item_id, $path);
                        } else {
                            $errors[] = "Error during the transfert";
                        }
                    }
                }
            }
        }
        if (count($errors) > 0) {
            (new View("manage_images"))->show([
                "item" => $item,
                "errors" => $errors
            ]);
        } else {
            $this->redirect("item", "manage_images", $item_id);
        }
    }

    public function browse() {
        // we retrive the user session
        $user = $this->get_user_or_false();

        // we check whether it's an existing user or a guest
        $is_guest = $user == false;

        // we retrieve items the user placed a bid on
        $user_bid_items = $is_guest ? [] : Item::get_user_bid_items($user->get_id());

        // we retrieve items available for him to purchase or place a bid 
        $user_available_items = $is_guest ? Item::get_all_available_items() : Item::get_user_available_items($user->get_id());

        (new View("browse_items"))->show(
            [
                "user_bid_items" => $user_bid_items,
                "user_available_items" => $user_available_items,
                "is_guest" => $is_guest
            ]
        );
    }

    public function purchases(): void {
        // we retrive the user session
        $user = $this->get_user_or_redirect();

        $purchases = Item::get_purchases($user->get_id());
        (new View("see_my_purchases"))->show([
            "purchases" => $purchases
        ]);
    }

    public function sales(): void {
        // we retrive the user session
        $user = $this->get_user_or_redirect();

        $sales = Item::get_sales($user->get_id());
        (new View("see_my_sales"))->show(
            [
                "sales" => $sales
            ]
        );
    }

    // Services AJAX
    public function search_fulltext_browse_service(): void {
        header('Content-Type: application/json');

        if (isset($_GET["search"]) && trim($_GET["search"]) !== "") {
            $search = trim($_GET["search"]);
            $current_user = $this->get_user_or_false();

            if (!$current_user) {
                echo json_encode([]);
            }

            $user_id = $current_user->get_id();

            $bid_items = Item::get_user_bid_items($user_id, $search);
            $available_items = Item::get_user_available_items($user_id, $search);

            $results = [];

            // les items sur lesquels on a participé
            foreach ($bid_items as $item) {
                $results[] = [
                    'id'              => $item->get_id(),
                    'title'           => $item->get_title(),
                    'description'     => $item->get_description(),
                    'owner_name'      => $item->get_owner_name(),
                    'main_image_url'  => $item->get_main_image_url(),
                    'price'           => $item->get_buy_now_price(),
                    'starting_bid'    => $item->get_starting_bid(),
                    'current_bid'     => $item->get_current_bid(),
                    'is_auction'      => $item->get_is_auction(),
                    'has_buy_now'     => $item->get_has_buy_now(),
                    'has_bids'        => true,
                    'buy_now_reached' => $item->get_buy_now_reached(),
                    'time_left'       => $item->get_time_left(),
                    'end_at'          => $item->get_end_at()->format('Y-m-d H:i:s'),
                    'created_at'      => $item->get_created_at()->format('Y-m-d H:i:s'),
                    'extra_images'    => count($item->get_image_urls()) - 1,
                    'is_highest_bidder' => ($item->get_user_bid_amount() == $item->get_current_bid() && $item->get_current_bid() > 0),
                ];
            }

            // les autres items
            foreach ($available_items as $item) {
                $results[] = [
                    'id'              => $item->get_id(),
                    'title'           => $item->get_title(),
                    'description'     => $item->get_description(),
                    'owner_name'      => $item->get_owner_name(),
                    'main_image_url'  => $item->get_main_image_url(),
                    'price'           => $item->get_buy_now_price(),
                    'starting_bid'    => $item->get_starting_bid(),
                    'current_bid'     => $item->get_current_bid(),
                    'is_auction'      => $item->get_is_auction(),
                    'has_buy_now'     => $item->get_has_buy_now(),
                    'has_bids'        => false,
                    'buy_now_reached' => $item->get_buy_now_reached(),
                    'time_left'       => $item->get_time_left(),
                    'end_at'          => $item->get_end_at()->format('Y-m-d H:i:s'),
                    'created_at'      => $item->get_created_at()->format('Y-m-d H:i:s'),
                    'extra_images'    => count($item->get_image_urls()) - 1,
                    'is_highest_bidder' => false,
                ];
            }

            echo json_encode($results);
        } else {
            echo json_encode([]);
        }
    }

    public function search_fulltext_my_items_service(): void {
        header('Content-Type: application/json');

        if (isset($_GET["search"]) && trim($_GET["search"]) !== "") {
            $search = trim($_GET["search"]);
            $current_user = $this->get_user_or_false();

            if (!$current_user) {
                echo json_encode([]);
            }

            $user_id = $current_user->get_id();
            $my_items = Item::get_items_by_owner($user_id, $search);

            $results = [];
            $now = new DateTime(AppTime::get_current_datetime());

            foreach ($my_items as $item) {
                $display_data = $this->prepare_item_data($item, $now);

                // sold, unsold ou active
                $is_buy_now = $item->get_buy_now_reached();
                $is_expired = $item->get_end_at() <= $now;
                $has_bids   = $item->get_bid_count() > 0;

                if ($is_buy_now || ($is_expired && $has_bids)) {
                    $context = 'sold';
                } elseif ($is_expired && !$has_bids) {
                    $context = 'unsold';
                } else {
                    $context = 'active';
                }

                $results[] = [
                    'id'              => $item->get_id(),
                    'title'           => $item->get_title(),
                    'description'     => $item->get_description(),
                    'owner_name'      => $current_user->get_pseudo(),
                    'main_image_url'  => $display_data->img_src,
                    'price'           => $item->get_buy_now_price(),
                    'starting_bid'    => $item->get_starting_bid(),
                    'current_bid'     => $item->get_current_bid(),
                    'is_auction'      => $item->get_is_auction(),
                    'has_buy_now'     => $item->get_has_buy_now(),
                    'time_left'       => $display_data->time_left,
                    'end_at'          => $item->get_end_at()->format('Y-m-d H:i:s'),
                    'created_at'      => $item->get_created_at()->format('Y-m-d H:i:s'),
                    'buy_now_reached' => $item->get_buy_now_reached(),
                    'extra_images'    => $display_data->extra_images,
                    'context'         => $context
                ];
            }
            echo json_encode($results);
        } else {
            echo json_encode([]);
        }
    }
}
