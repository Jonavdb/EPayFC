<?php

require_once "framework/Controller.php";
require_once "model/User.php";
require_once "model/Item.php";
require_once "model/Bid.php";
require_once "utils/AppTime.php";
require_once "utils/ImageTool.php";



class ControllerItem extends Controller
{
    public function index(): void
    {
        $this->redirect("item", "list");
    }

    // My items
    public function list(): void
    {

        $user = $this->get_user_or_redirect();
        $items = Item::get_items_by_owner($user->id);

        $active_items = [];
        $sold_items = [];
        $unsold_items = [];
        $now = new DateTime();

        foreach ($items as $item) {
            // 1. Préparation des données d'affichage (photos des items, etc...)
            $display_data = $this->prepare_item_data($item, $now);

            // 2. Tri dans les catégories
            if ($item->end_at > $now) {
                $display_data->context = 'active';
                $active_items[] = $display_data;
            } else {
                $is_sold = ($item->bid_count > 0) || ($item->buy_now_reached);

                if ($is_sold) {
                    $display_data->context = 'sold';
                    $sold_items[] = $display_data;
                } else {
                    $display_data->context = 'unsold';
                    $unsold_items[] = $display_data;
                }
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
    private function prepare_item_data(Item $item, DateTime $now): object
    {
        $is_closed = $item->end_at <= $now;

        // Calcul du texte temps
        if ($is_closed) {
            $time_left_str = "Closed";
        } else {
            $diff = $now->diff($item->end_at);
            $time_left_str = ($diff->days > 0) ? $diff->days . " days left" : $diff->h . "h " . $diff->i . "m left";
        }

        // Image
        $img_src = !empty($item->main_image_url) ? $item->main_image_url : "uploads/items/item_placeholder.png";

        // Compteur images
        $total_image_count = is_array($item->image_urls) ? count($item->image_urls) : 0;
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
    public function delete($id = null): void
    {
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

        // LOGIQUE DE SUPPRESSION (Si on a cliqué sur "Delete" - POST)
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            Item::delete($id);
            $this->redirect("item", "list");
        } else {
            // LOGIQUE D'AFFICHAGE
            (new View("delete_item"))->show([
                "item" => $item,
                "user" => $user
            ]);
        }
    }

    public function open(): void
    {
        // 2. On regarde si le premier argument (l'ID) existe
        if (isset($_GET['param1'])) {
            $id = (int)$_GET['param1']; // On récupère le 8 de l'URL ".../index/8"
        } else {
            $id = 3; // Valeur par défaut si l'URL est juste ".../index"
        }
        $image_index = isset($_GET['param2']) ? (int)$_GET['param2'] : 0;
        $item = Item::get_item_by_id($id);
        if (isset($item->image_urls[$image_index])) {
            $item->main_image_url = $item->image_urls[$image_index];
        }

        $bids = Bid::get_bid_by_item_id($id);
        $user = $this->get_user_or_false();
        $current_user_pseudo = $user ?  $user->pseudo : null;


        (new View("open_item"))->show([
            "item" => $item,
            "bids" => $bids,
            "current_user" => $current_user_pseudo,
            'selected_image' => $image_index
        ]);
    }

    public function new_bid()
    {

        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0.0;

        $user = $this->get_user_or_redirect();
        $current_user_pseudo = $user->pseudo;

        if ($item_id > 0 && $amount > 0) {
            Bid::create_bid($current_user_pseudo, $item_id, $amount);
        }

        $this->redirect("item", "open", $item_id);
    }

    public function buy_now()
    {
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $user = $this->get_user_or_redirect();
        $current_user_pseudo = $user->pseudo;
        if ($item_id > 0) {

            $item = Item::get_item_by_id($item_id);

            if ($item && $item->has_buy_now && !$item->buy_now_reached) {
                Bid::create_bid($current_user_pseudo, $item_id, $item->buy_now_price);
            }
        }
        $this->redirect("item", "open", $item_id);
    }


    public function add()
    {
        $errors = [];
        $user = $this->get_user_or_redirect();
        $now = new DateTime(AppTime::get_current_datetime());
        $end = (clone $now)->modify('+7 days');

        $item = new Item(
            0.0,
            null,
            0,
            "",
            "",
            $user->pseudo,
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
            $item->title = trim($_POST['title']);
            $item->description = trim($_POST['description']);
            $item->duration_days = (int)$_POST['duration'];

            $starting_bid = ($_POST['starting_bid'] !== '') ? $_POST['starting_bid'] : null;
            $buy_now_price = ($_POST['instant'] !== '') ? $_POST['instant'] : null;
            $sale_price = ($_POST['direct'] !== '') ? $_POST['direct'] : null;

            if (strlen($item->title) === 0 || empty($item->title)) {
                $errors['empty'] = "Title is required";
            }

            if (strlen($item->title) < 3 || strlen($item->title) > 255) {
                $errors['title'] = "Title lenght must be between 3 and 255 characters.";
            }

            if ($item->duration_days < 1) {
                $errors['duration'] = "Duration must be at least 1 day";
            }

            if ($starting_bid !== null && $starting_bid > 0) {
                $item->starting_bid = $starting_bid;
                $item->is_auction = true;
                $item->is_direct_sale = false;

                if ($buy_now_price !== null && $buy_now_price > 0) {
                    if ($buy_now_price < $starting_bid) {
                        $errors['buy_now_price'] = "Instant purchase price must be higher than starting bid price";
                    }
                    $item->buy_now_price = $buy_now_price;
                    $item->has_buy_now = true;
                } else {
                    $item->buy_now_price = null;
                    $item->has_buy_now = false;
                }

                if ($sale_price !== null && $sale_price > 0) {
                    $errors['sale_price'] = "You can't put a direct sale price and a starting bid.";
                }
            } elseif ($sale_price !== null && $sale_price > 0) {
                $item->buy_now_price = $sale_price;
                $item->is_auction = false;
                $item->is_direct_sale = true;
                $item->has_buy_now = false;
            } else {
                $errors['sale'] = "You must either fill a starting bid or a direct sale price.";
            }

            if (empty($errors)) {
                $item->save();
                $this->redirect("item", "open", $item->id);
            }
        }

        (new View("add_edit_item"))->show([
            "item" => $item,
            "errors" => $errors
        ]);
    }

    public function edit()
    {
        $errors = [];
        $user = $this->get_user_or_redirect();

        $id = $_GET['param1'] ?? $_POST['id'] ?? null;

        if (!$id) {
            $this->redirect("item", "index");
            return;
        }

        $item = Item::get_item_by_id($id);

        if ($item) {
            if ($item->owner_name !== $user->pseudo || ($item->is_auction && $item->has_bids)) {
                $this->redirect("item", "open", $item->id);
                exit();
            }
        } else {

            $this->redirect("item", "index");
            return;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $item->title = trim($_POST['title']);
            $item->description = trim($_POST['description']);
            $item->duration_days = (int)$_POST['duration'];

            $starting_bid = ($_POST['starting_bid'] !== '') ? $_POST['starting_bid'] : null;
            $buy_now_price = ($_POST['instant'] !== '') ? $_POST['instant'] : null;
            $sale_price = ($_POST['direct'] !== '') ? $_POST['direct'] : null;

            if (strlen($item->title) === 0 || empty($item->title)) {
                $errors['empty'] = "Title is required";
            }

            if (strlen($item->title) < 3 || strlen($item->title) > 255) {
                $errors['title'] = "Title lenght must be between 3 and 255 characters.";
            }

            if ($item->duration_days < 1) {
                $errors['duration'] = "Duration must be at least 1 day";
            }

            if ($starting_bid !== null && $starting_bid > 0) {
                $item->starting_bid = $starting_bid;
                $item->is_auction = true;
                $item->is_direct_sale = false;

                if ($buy_now_price !== null && $buy_now_price > 0) {
                    if ($buy_now_price < $starting_bid) {
                        $errors['buy_now_price'] = "Instant purchase price must be higher than starting bid price";
                    }
                    $item->buy_now_price = $buy_now_price;
                    $item->has_buy_now = true;
                } else {
                    $item->buy_now_price = null;
                    $item->has_buy_now = false;
                }

                if ($sale_price !== null && $sale_price > 0) {
                    $errors['sale_price'] = "You can't put a direct sale price and a starting bid.";
                }
            } elseif ($sale_price !== null && $sale_price > 0) {
                $item->starting_bid = 0;
                $item->buy_now_price = $sale_price;
                $item->is_auction = false;
                $item->is_direct_sale = true;
                $item->has_buy_now = false;
            } else {
                $errors['sale'] = "You must either fill a starting bid or a direct sale price.";
            }

            if (empty($errors)) {
                $item->save();
                $this->redirect("item", "open", $item->id);
            }
        }

        (new View("add_edit_item"))->show([
            "item" => $item,
            "errors" => $errors
        ]);
    }



    public function manage_images(): void
    {

        if (isset($_GET['param1'])) {
            $id = (int)$_GET['param1'];
        } else {
            $this->redirect("BrowseItems", "show_items");
            return;
        }

        $item = Item::get_item_by_id($id);

        $user = $this->get_user_or_redirect();
        $current_user = $user->pseudo;

        if (!$item) {
            $this->redirect("BrowseItems", "show_items");
            return;
        }

        if ($item->owner_name !== $current_user) {
            $this->redirect("item", "open", $id);
            return;
        }

        (new View("manage_images"))->show([
            "item" => $item
        ]);
    }

    public function move_image_left()
    {
        $image_index = isset($_POST['image_index']) ? (int)$_POST['image_index'] : 0;
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        if ($item_id > 0) {
            Item::change_pics_left($image_index, $item_id);
        }
        $this->redirect("item", "manage_images", $item_id);
    }

    public function move_image_right()
    {
        $image_index = isset($_POST['image_index']) ? (int)$_POST['image_index'] : 0;
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        if ($item_id > 0) {
            Item::change_pics_right($image_index, $item_id);
        }
        $this->redirect("item", "manage_images", $item_id);
    }

    public function delete_images()
    {
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $filename = isset($_POST['image_filename']) ? $_POST['image_filename'] : '';

        if ($item_id > 0 && !empty($filename)) {
            Item::delete_images($filename, $item_id);
        }
        $this->redirect("item", "manage_images", $item_id);
    }

    public function add_images()
    {
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

        if ($item_id > 0 && isset($_FILES['image_files'])) {
            $files = $_FILES['image_files'];
            $count = count($files['name']);

            for ($i = 0; $i < $count; $i++) {
                $file = [
                    'name' => $files['name'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'size'     => $files['size'][$i],
                    'error'    => $files['error'][$i]
                ];

                if ($file['error'] === 0) {
                    $path = ImageTools::upload_and_resize($file, 'items', $item_id);

                    if ($path) {
                        Item::add_picture($item_id, $path);
                    }
                }
            }
        }
        $this->redirect("item", "manage_images", $item_id);
    }

    public function browse()
    {
        // we retrive the user session
        $user = $this->get_user_or_false();

        // we check whether it's an existing user or a guest
        $is_guest = $user == false;

        // we retrieve items the user placed a bid on
        $user_bid_items = $is_guest ? [] : Item::get_user_bid_items($user->id);

        // we retrieve items available for him to purchase or place a bid 
        $user_available_items = $is_guest ? Item::get_all_available_items() : Item::get_user_available_items($user->id);

        (new View("browse_items"))->show(
            [
                "user_bid_items" => $user_bid_items,
                "user_available_items" => $user_available_items,
                "is_guest" => $is_guest
            ]
        );
    }

    public function purchases(): void
    {
        // we retrive the user session
        $user = $this->get_user_or_false();

        $purchases = Item::get_purchases($user->id);
        (new View("see_my_purchases"))->show([
            "purchases" => $purchases
        ]);
    }

    public function sales(): void
    {
        // we retrive the user session
        $user = $this->get_user_or_false();

        $sales = Item::get_sales($user->id);
        (new View("see_my_sales"))->show(
            [
                "sales" => $sales
            ]
        );
    }
}
