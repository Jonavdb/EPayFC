<?php
require_once "framework/Controller.php";
require_once "Model/Category.php";


class ControllerCategory extends Controller {
    public function index(): void {
        $this->redirect("category", "manage");
    }

    // Manage
    public function manage(): void {

        // On récupère la liste des catégories actuelles
        $categories = Category::get_categories();

        // On les transfère à la vue
        (new View("manage_categories"))->show([
            "categories" => $categories
        ]);
    }

    public function handle_action(): void {
        $action_type = $_POST['action'];
        $category_id = $_POST['id'];
        switch ($action_type) {
            case "up":
                self::move_up($category_id);
                break;
            case "down":
                self::move_down($category_id);
                break;
            case "save":
                self::save($category_id);
                break;
            case "delete":
                self::delete($category_id);
                break;
            case null:
                break;
        }
    }

    public function move_up(int $id): void {
        echo "UP!";

        $from = Category::get_category_from_id($id);
        $priority_from = $from->get_priority();

        $to = Category::get_category_with_next_lower_priority($from->get_priority());
        $priority_to = $to->get_priority();

        // self::swap(from->get_id(), to->get_id(), SwapType::UP);

        $to->set_priority(-1);
        $from->set_priority($priority_to);
        $to->set_priority($priority_from);

        // on reload la page
        $this->redirect("category", "index");
    }

    public function move_down(int $id): void {
        echo "DOWN!";
        $from = Category::get_category_from_id($id);
        $priority_from = $from->get_priority();

        $to = Category::get_category_with_next_higher_priority($from->get_priority());
        $priority_to = $to->get_priority();

        // self::swap(from->get_id(), to->get_id(), SwapType::UP);

        $to->set_priority(-1);
        $from->set_priority($priority_to);
        $to->set_priority($priority_from);

        // on reload la page
        $this->redirect("category", "index");
    }

    public function save(int $id): void {
        echo "SAVE!";
    }

    public function delete(int $id): void {
        echo "DELETE!";
    }

    public function add_category(): void {

        // on vérifie si l'utilisateur ecrit quelque chose
        if (!isset($_POST['name']) || empty(trim($_POST['name']))) {
            header("Location: index");
            exit(); // Stop script execution
        }

        // on récupère l'input
        $raw_name = $_POST['name'];

        // on check si l'input est valide
        $errors = Category::validate($raw_name);

        // si pas d'erreurs..
        if (empty($errors)) {
            // success..
            // on ajoute la catégorie dans la db..
            Category::add(trim($raw_name));     // puis on refresh la page..      
            header("Location: index");       // puis on arrête la fonction
            exit();
        } else {
            // echec.. affiche la page avec les erreurs
            (new View("manage_categories"))->show([
                "categories" => Category::get_categories(),
                "errors" => $errors
            ]);
        }
    }
}
