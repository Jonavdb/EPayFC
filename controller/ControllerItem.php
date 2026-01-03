<?php

require_once "framework/Controller.php";
require_once "model/User.php";
require_once "model/Item.php";

class ControllerItem extends Controller
{
    public function index(): void
    {
        $this->redirect("item", "list");
    }

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

    /* Prépare les données pour l'affichage */
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

    public function add_edit_item()
    {

        $errors = [];
        $item = null;
        $user = $this->get_user_or_redirect();

        if (isset($_GET['param1']) && !empty($_GET['param1'])) {
            $item = Item::get_item_by_id($_GET['param1']);
        } else {
            $now = new DateTime(AppTime::get_current_datetime());
            $end = (clone $now)->modify('+7 days');

            $item = new Item(
                0.0,
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
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $item->title = trim($_POST['title']);
            $item->description = trim($_POST['description']);
            $item->duration_days = (int)$_POST['duration'];

            $starting_bid = ($_POST['starting_bid'] !== '') ? $_POST['starting_bid'] : null;
            $buy_now_price = ($_POST['instant'] !== '') ? $_POST['instant'] : null;
            $sale_price = ($_POST['direct'] !== '') ? $_POST['direct'] : null;

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
                        $errors['buy_now_price'] = "Buy now price must be higher than starting bid price";
                    }
                    $item->buy_now_price = $buy_now_price;
                    $item->has_buy_now = true;
                } else {
                    $item->buy_now_price = 0.0;
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
                //$item->save(); TODO
                $this->redirect("Item, list");
            }
        }


        (new View("add_edit_item"))->show([
            "item" => $item,
            "errors" => $errors
        ]);
    }
}
