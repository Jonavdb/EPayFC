<?php

require_once "framework/Controller.php";
require_once "model/User.php";
require_once "model/Item.php";
require_once "model/Category.php";
require_once "model/Bid.php";
require_once "utils/AppTime.php";
require_once "utils/ImageTool.php";
require_once "utils/UrlEncoder.php";
require_once "utils/GoBack.php";

class ControllerItem extends Controller {
    public function index(): void {
        $this->redirect("item", "list");
    }

    // My items
    public function list(): void {
        $user = $this->get_user_or_redirect();

        // on lit le paramètre d'URL et on le décode
        $encoded_search = isset($_GET['param1']) ? $_GET['param1'] : '';
        $search_query = "";
        $category_id = null;

        if ($encoded_search !== '') {
            $decoded = UrlEncoder::url_safe_decode($encoded_search);

            if (is_array($decoded)) {
                if (isset($decoded['search'])) {
                    $search_query = $decoded['search'];
                }
                if (isset($decoded['category_id'])) {
                    $category_id = (int)$decoded['category_id'];
                }
            }
        }

        $items = Item::get_items_by_owner($user->get_id(), $search_query, $category_id);

        $active_items = [];
        $sold_items = [];
        $unsold_items = [];
        $now = new DateTime(AppTime::get_current_datetime());

        foreach ($items as $item) {
            // Préparation des données d'affichage (photos des items, etc...)
            $display_data = $this->prepare_item_data($item, $now, 'my-items', $user->get_pseudo(), $encoded_search);

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
            "unsold_items" => $unsold_items,
            "is_guest" => !$this->user_logged(),
            "search_query" => $search_query,
            "categories" => Category::get_categories(),
            "selected_category_id" => $category_id
        ]);
    }

    /* Helper partagé : Prépare les données d'affichage d'une carte item */
    private function prepare_item_data(
        Item $item,
        DateTime $now,
        string $card_context = 'my-items',
        string $owner_name = '',
        string $encoded_search = ''
    ): object {
        $is_closed = $item->get_end_at() <= $now;

        // Calcul du texte temps + couleur
        if ($is_closed) {
            $time_left_str = "Closed";
            $time_color    = "text-secondary";
        } else {
            $diff = $now->diff($item->get_end_at());
            $time_left_str = ($diff->days > 0) ? $diff->days . " days left" : $diff->h . "h " . $diff->i . "m left";
            $time_color    = ($diff->days < 1) ? "text-warning" : "text-secondary";
        }

        // Couleurs pour le time_left
        $time_color = $is_closed ? 'text-secondary' : 'text-success';
        $price_color = $item->get_has_buy_now() ? 'text-primary' : 'text-warning';

        $raw_price = $item->get_has_buy_now() ? $item->get_buy_now_price() : $item->get_starting_bid();
        $price_display = $raw_price ? number_format((float)$raw_price, 2, ',', '.') : "0,00";
        $current_bid_display = number_format((float)$item->get_current_bid(), 2, ',', '.');

        // Image
        $img_src = !empty($item->get_main_image_url()) ? $item->get_main_image_url() : "uploads/items/item_placeholder.png";

        // Compteur images
        $total_image_count = is_array($item->get_image_urls()) ? count($item->get_image_urls()) : 0;
        $extra_image_count = max(0, $total_image_count - 1);

        // Lien et propriétaire selon le contexte
        $href  = ($card_context === 'my-items')
            ? "item/open/" . $item->get_id() . "/from-items"
            : "item/open/" . $item->get_id() . "/from-browse";

        $is_highest_bidder = ($card_context === 'browse-bidding')
            ? ($item->get_user_bid_amount() == $item->get_current_bid() && $item->get_current_bid() > 0)
            : false;

        if ($encoded_search !== '') {
            $href .= "/" . $encoded_search;
        }

        $has_buy_now = $item->get_has_buy_now();
        $is_auction  = $item->get_is_auction() || $card_context === 'browse';
        $is_direct   = $item->get_is_direct_sale();

        if ($has_buy_now || $is_direct) {
            $price_display = number_format($item->get_buy_now_price(), 2, ',', '.');
            $price_color   = 'text-primary';
        } elseif ($is_auction) {
            $price_display = number_format($item->get_starting_bid(), 2, ',', '.');
            $price_color   = 'text-warning';
        } else {
            $price_display = null;
            $price_color   = '';
        }

        $show_current_bid = $is_auction;
        $current_bid_display = ($item->get_current_bid() !== null)
            ? number_format($item->get_current_bid(), 2, ',', '.')
            : null;

        return (object) [
            'raw_item'             => $item,
            'time_left'            => $time_left_str,
            'time_color'           => $time_color,
            'img_src'              => $img_src,
            'extra_images'         => $extra_image_count,
            'context'              => '',
            'card_context'         => $card_context,
            'href'                 => $href,
            'owner'                => htmlspecialchars($owner_name),
            'price_color'          => $price_color,
            'price_display'        => $price_display,
            'is_highest_bidder'    => $is_highest_bidder,
            'show_current_bid'     => $show_current_bid,
            'current_bid_display'  => $current_bid_display,
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
                "user" => $user,
                "is_guest" => !$this->user_logged()
            ]);
        }
    }

    // Open item
    public function open(): void {
        if (isset($_GET['param1'])) {
            $id = (int)$_GET['param1'];
        } else {
            $this->redirect('item', 'browse');
            return;
        }

        $item = Item::get_item_by_id($id);

        if (empty($item)) {
            $this->redirect('item', 'browse');
            return;
        }

        $context = isset($_GET['param2']) ? $_GET['param2'] : 'from-browse';
        $param3 = isset($_GET['param3']) ? $_GET['param3'] : '';
        $param4 = isset($_GET['param4']) ? $_GET['param4'] : '';
        $item_categories = Category::get_item_categories($id);

        var_dump("ici ==================>", $item_categories);

        // param3 numérique = index image (sans encoded_search)
        // param3 string = encoded_search, param4 = index image
        if (is_numeric($param3)) {
            $image_index = (int)$param3;
            $encoded_search = '';
        } else {
            $encoded_search = $param3;
            $image_index = is_numeric($param4) ? (int)$param4 : 0;
        }

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
            'selected_image' => $image_index,
            "is_guest" => !$this->user_logged(),
            "context" => $context,
            "item_categories" => $item_categories,
            "encoded_search" => $encoded_search
        ]);
    }

    public function new_bid() {
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0.0;

        $user = $this->get_user_or_redirect();
        $current_user_pseudo = $user->get_pseudo();

        $encoded_search = isset($_POST['encoded_search']) ? $_POST['encoded_search'] : '';
        $context = isset($_POST['context']) ? $_POST['context'] : 'from-browse';

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

        $redirect_params = ['item', 'open', $item_id, $context];

        if ($encoded_search !== '') {
            $redirect_params[] = $encoded_search;
        }


        if ($item->get_end_at() < $now) {
            $this->redirect(...$redirect_params);
            return;
        }

        if ($item_id > 0 && $amount > 0) {
            Bid::create_bid($current_user_pseudo, $item_id, $amount);
        }

        $this->redirect(...$redirect_params);
    }

    public function buy_now() {
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

        $user = $this->get_user_or_redirect();
        $current_user_pseudo = $user->get_pseudo();

        $encoded_search = isset($_POST['encoded_search']) ? $_POST['encoded_search'] : '';
        $context = $_POST['context'] ?? $_GET['param2'] ?? 'from-browse';

        if ($item_id > 0) {
            $item = Item::get_item_by_id($item_id);

            if ($item && $item->get_owner_name() !== $current_user_pseudo && $item->get_has_buy_now() && !$item->get_buy_now_reached()) {
                Bid::create_bid($current_user_pseudo, $item_id, $item->get_buy_now_price());
            }
        }

        $redirect_params = ['item', 'open', $item_id, $context];

        if ($encoded_search !== '') {
            $redirect_params[] = $encoded_search;
        }

        $this->redirect(...$redirect_params);

        // $redirect_url = GoBack::smart_href("item/open/" . $item->get_id());
        // $url_parts = explode('/', $redirect_url);
        // $this->redirect(...$url_parts);
    }

    public function add() {
        $user = $this->get_user_or_redirect();

        $now = new DateTime(AppTime::get_current_datetime());
        $end = (clone $now)->modify('+7 days');

        $categories = Category::get_categories();
        $checkedCategories = $_POST['categories'] ?? [];

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

            // validation côté serveur (quand JS est désactivé)
            if (count($checkedCategories) > 3) {
                $errors['categories'] = "You cannot select more than 3 categories.";
            }

            if (empty($errors)) {
                $item->save();

                // boucle sur les catégories checkées pour les rajouter
                foreach ($checkedCategories as $categoryId) {
                    $item->add_categories($item->get_id(), (int)$categoryId);
                }

                $this->redirect("item", "open", $item->get_id());
            }
        }

        (new View("add_edit_item"))->show([
            "item" => $item,
            "categories" => $categories,
            "errors" => $errors,
            "is_guest" => !$this->user_logged()
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

        if (strlen($item->get_title()) == 0 || empty($item->get_title())) {
            $errors['empty'] = "Title is required";
        }

        if (strlen($item->get_title()) < $min_lenght || strlen($item->get_title()) > $max_lenght) {

            $errors['title'] = "Title length must be between $min_lenght and $max_lenght characters.";
        }

        if ($item->get_duration_days() < $min_duration || $item->get_duration_days() > $max_duration) {
            $errors['duration'] = "Duration must be at least $min_duration day and at most $max_duration days";
        }

        // ask if we keep
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
        $categories = Category::get_categories();

        $context = $_POST['context'] ?? $_GET['param2'] ?? 'from-browse';
        $encoded_search = $_POST['encoded_search'] ?? $_GET['param3'] ?? '';

        $checkedCategories = $_POST['categories'] ?? [];

        if (!$id) {
            $this->redirect("item", "index");
            return;
        }

        $item = Item::get_item_by_id($id);

        if ($item) {
            if ($item->get_owner_name() !== $user->get_pseudo() || ($item->get_is_auction() && $item->get_has_bids())) {
                // // Redirection sécurisée
                if ($encoded_search !== '') {
                    $this->redirect("item", "open", $item->get_id(), $context, $encoded_search);
                } else {
                    $this->redirect("item", "open", $item->get_id(), $context);
                }
                exit();
            }
        } else {
            $this->redirect("item", "index");
            return;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $errors = $this->validate_and_fill($item);

            // validation côté serveur (quand JS est désactivé)
            if (count($checkedCategories) > 3) {
                $errors['categories'] = "You cannot select more than 3 categories.";
            }

            if (empty($errors)) {
                $item->save();
                $item->remove_categories($id);  // supprime les catégories existantes

                // boucle sur les catégories checkées pour les rajouter
                foreach ($checkedCategories as $categoryId) {
                    $item->add_categories($id, (int)$categoryId);
                }

                if ($encoded_search !== '') {
                    $this->redirect("item", "open", $item->get_id(), $context, $encoded_search);
                } else {
                    $this->redirect("item", "open", $item->get_id(), $context);
                }
            }
        }

        (new View("add_edit_item"))->show([
            "item" => $item,
            "categories" => $categories,
            "errors" => $errors,
            "is_guest" => !$this->user_logged(),
            "context" => $context,
            "encoded_search" => $encoded_search
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
            "item" => $item,
            "is_guest" => !$this->user_logged()
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
        $redirect_url = GoBack::smart_href("item/manage_images/" . $item_id);
        $url_parts = explode('/', $redirect_url);
        $this->redirect(...$url_parts);
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
        $redirect_url = GoBack::smart_href("item/manage_images/" . $item_id);
        $url_parts = explode('/', $redirect_url);
        $this->redirect(...$url_parts);
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
        $redirect_url = GoBack::smart_href("item/manage_images/" . $item_id);
        $url_parts = explode('/', $redirect_url);
        $this->redirect(...$url_parts);
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
                "errors" => $errors,
                "is_guest" => !$this->user_logged()
            ]);
        } else {
            $redirect_url = GoBack::smart_href("item/manage_images/" . $item_id);
            $url_parts = explode('/', $redirect_url);
            $this->redirect(...$url_parts);
        }
    }

    public function browse() {
        $user = $this->get_user_or_false();
        $is_guest = $user == false;
        $now = new DateTime(AppTime::get_current_datetime());

        $encoded_search = isset($_GET['param1']) ? $_GET['param1'] : '';
        $search_query = "";
        $category_id = null;

        if ($encoded_search !== '') {
            $decoded = UrlEncoder::url_safe_decode($encoded_search);

            if (is_array($decoded)) {
                if (isset($decoded['search'])) {
                    $search_query = $decoded['search'];
                }
                if (isset($decoded['category_id'])) {
                    $category_id = (int)$decoded['category_id'];
                }
            }
        }

        $raw_bid_items = $is_guest ? [] : Item::get_user_bid_items($user->get_id(), $search_query, $category_id);
        $user_bid_items = array_map(fn($item) => $this->prepare_item_data($item, $now, 'browse-bidding', $item->get_owner_name(), $encoded_search), $raw_bid_items);

        $raw_available_items = $is_guest ? Item::get_all_available_items($category_id) : Item::get_user_available_items($user->get_id(), $search_query, $category_id);
        $user_available_items = array_map(fn($item) => $this->prepare_item_data($item, $now, 'browse', $item->get_owner_name(), $encoded_search), $raw_available_items);

        (new View("browse_items"))->show(
            [
                "user_bid_items" => $user_bid_items,
                "user_available_items" => $user_available_items,
                "is_guest" => $is_guest,
                "search_query" => $search_query,
                "categories" => Category::get_categories(),
                "selected_category_id" => $category_id
            ]
        );
    }

    public function purchases(): void {
        // we retrive the user session
        $user = $this->get_user_or_redirect();

        $purchases = Item::get_purchases($user->get_id());
        (new View("see_my_purchases"))->show([
            "purchases" => $purchases,
            "is_guest" => !$this->user_logged()
        ]);
    }

    public function sales(): void {
        // we retrive the user session
        $user = $this->get_user_or_redirect();

        $sales = Item::get_sales($user->get_id());
        (new View("see_my_sales"))->show(
            [
                "sales" => $sales,
                "is_guest" => !$this->user_logged()
            ]
        );
    }

    // Services AJAX
    public function get_js_images_service(): void {
        $item_id = (int)$_GET['param1'];
        $pictures = ItemPicture::get_pictures_by_item($item_id); // récupère toutes les images d'un objet
        $urls = array_map(fn($pic) => $pic->get_picture_path(), $pictures); // map les informations en spécifiant que pour chaque image de la variables pictures, on récupère uniquement son chemin
        header('Content-Type: application/json'); // précise au navigateur qu'il va recevoir du Json
        echo json_encode($urls); // encode les infos du array en Json et l'affiche au navigateur
        exit;
    }

    public function update_image_order_service(): void {
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0; // on récupère l'ID de l'objet
        $order = isset($_POST['order']) ? (array)$_POST['order'] : []; // on récupère l'ordre des objets

        $user = $this->get_user_or_redirect(); // on récupère le user connecté
        $item = Item::get_item_by_id($item_id); // on récupère l'objet lié à l'ID

        if (!$item || $item->get_owner_name() !== $user->get_pseudo()) {
            http_response_code(403); // si pas d'objet ou si on appelle la méthode en étant pas le owner, 403 accès forbidden
            exit; // on stop
        }
        Item::update_image_priority($item_id, $order); // on update les priorités
        http_response_code(200); // on renvoie un code 200 pour dire que c'est OK 
        exit;
    }

    public function move_image_right_service(): void {
        header('Content-Type: application/json');

        if (
            isset($_GET['param1']) && trim($_GET['param1']) !== '' &&
            isset($_GET['param2']) && trim($_GET['param2']) !== ''
        ) {
            $image_id = (int)$_GET['param1'];
            $item_id = (int)$_GET['param2'];

            $current_user = $this->get_user_or_false();

            if (!$current_user) {
                echo json_encode([]);
                exit;
            }

            $image_item = Item::change_pics_right($image_id, $item_id);
            echo json_encode(["image_item" => $image_item]);
        }
    }

    public function move_image_left_service(): void {
        header('Content-Type: application/json');

        if (
            isset($_GET['param1']) && trim($_GET['param1']) !== '' &&
            isset($_GET['param2']) && trim($_GET['param2']) !== ''
        ) {
            $image_id = (int)$_GET['param1'];
            $item_id = (int)$_GET['param2'];

            $current_user = $this->get_user_or_false();

            if (!$current_user) {
                echo json_encode([]);
                exit;
            }

            $image_item = Item::change_pics_left($image_id, $item_id);
            echo json_encode(["image_item" => $image_item]);
        }
    }

    public function delete_images_service() {
        header('Content-Type: application/json');

        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $filename = isset($_POST['image_filename']) ? $_POST['image_filename'] : '';

        if ($item_id > 0 && !empty($filename)) {
            $user = $this->get_user_or_redirect();
            $item = Item::get_item_by_id($item_id);
            if (!$item || $item->get_owner_name() !== $user->get_pseudo()) {
                echo json_encode([]);
                exit;
            }
            Item::delete_images($filename, $item_id);
            echo json_encode(["image_delete" => $filename]);
        } else {
            echo json_encode([]);
        }
    }

    public function search_fulltext_browse_service(): void {
        header('Content-Type: application/json');
        $search = isset($_GET["search"]) ? trim($_GET["search"]) : '';
        $category_id = isset($_GET["category_id"]) ? (int)$_GET["category_id"] : null;

        if ($search !== '' || $category_id !== null) {
            $search = trim($_GET["search"]);
            $current_user = $this->get_user_or_false();
            $category_id = isset($_GET["category_id"]) && $_GET["category_id"] !== '' ? (int)$_GET["category_id"] : null;

            $encoded_search = UrlEncoder::url_safe_encode(['search' => $search, 'category_id' => $category_id]);

            if (!$current_user) {
                echo json_encode([]);
                exit; // Sécurité
            }

            $user_id = $current_user->get_id();

            $bid_items = Item::get_user_bid_items($user_id, $search, $category_id);
            $available_items = Item::get_user_available_items($user_id, $search, $category_id);

            $results = [];
            $now = new DateTime(AppTime::get_current_datetime());

            // les items sur lesquels on participe (contexte 'browse-bidding')
            foreach ($bid_items as $item) {
                $display_data = $this->prepare_item_data($item, $now, 'browse-bidding', $item->get_owner_name(), $encoded_search);

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
                    'is_highest_bidder' => $display_data->is_highest_bidder, // Calcul sécurisé du helper
                    'buy_now_reached' => $item->get_buy_now_reached(),
                    'time_left'       => $display_data->time_left,
                    'end_at'          => $item->get_end_at()->format('Y-m-d H:i:s'),
                    'created_at'      => $item->get_created_at()->format('Y-m-d H:i:s'),
                    'extra_images'    => $display_data->extra_images,
                ];
            }

            // les autres items (contexte 'browse')
            foreach ($available_items as $item) {
                $display_data = $this->prepare_item_data($item, $now, 'browse', $item->get_owner_name(), $encoded_search);

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
                    'is_highest_bidder' => false, // jamais de highest bidder ici
                    'buy_now_reached' => $item->get_buy_now_reached(),
                    'time_left'       => $display_data->time_left,
                    'end_at'          => $item->get_end_at()->format('Y-m-d H:i:s'),
                    'created_at'      => $item->get_created_at()->format('Y-m-d H:i:s'),
                    'extra_images'    => $display_data->extra_images,
                ];
            }

            echo json_encode([
                'encoded_search' => $encoded_search,
                'items' => $results
            ]);
            exit;
        } else {
            echo json_encode(['encoded_search' => '', 'items' => []]);
            exit; //force l'arrêt
        }
    }

    public function search_fulltext_my_items_service(): void {
        header('Content-Type: application/json');
        $search = isset($_GET["search"]) ? trim($_GET["search"]) : '';
        $category_id = isset($_GET["category_id"]) && $_GET["category_id"] !== '' ? (int)$_GET["category_id"] : null;

        if ($search !== '' || $category_id !== null) {
            $search = trim($_GET["search"]);
            $current_user = $this->get_user_or_false();

            $encoded_search = UrlEncoder::url_safe_encode(['search' => $search, 'category_id' => $category_id]);

            if (!$current_user) {
                echo json_encode([]);
                Tools::abort("User est false");
            }

            $user_id = $current_user->get_id();
            $my_items = Item::get_items_by_owner($user_id, $search, $category_id);

            $results = [];
            $now = new DateTime(AppTime::get_current_datetime());

            foreach ($my_items as $item) {
                $display_data = $this->prepare_item_data($item, $now, 'my-items-bidding', $item->get_owner_name(), $encoded_search);

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
                    'buy_now_reached' => $item->get_buy_now_reached(),
                    'extra_images'    => $display_data->extra_images,
                    'context'         => $context
                ];
            }

            echo json_encode([
                'encoded_search' => $encoded_search,
                'items' => $results
            ]);
        } else {
            echo json_encode(['encoded_search' => '', 'items' => []]);
        }
    }
}
