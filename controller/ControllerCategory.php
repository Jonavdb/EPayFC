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
        $from = Category::get_category_from_id($id);
        Category::move($from->get_id(), MoveDirection::UP);

        // on reload la page
        $this->redirect("category", "index");
    }

    public function move_down(int $id): void {
        $from = Category::get_category_from_id($id);
        Category::move($from->get_id(), MoveDirection::DOWN);

        // on reload la page
        $this->redirect("category", "index");
    }

    public function save(int $id): void {
        // on récupère l'input
        $raw_name = $_POST['name'];

        $category_from_name = Category::get_category_from_name(trim($raw_name));
        $category_from_id   = Category::get_category_from_id($id);

        // on ne fait rien si l'input est le même que le nom de la categorie qui était déjà là
        if ($category_from_name) {
            if ($category_from_name->get_name() === $category_from_id->get_name()) {
                $this->redirect("category", "index");
                return;
            }
        }

        // on check si l'input est valide
        $errors = Category::validate($raw_name);

        $to_save = null;

        // si pas d'erreurs..
        if (empty($errors)) {
            // success..
            // on sauvegarde les changements dans la db
            $to_save = Category::get_category_from_id($id);
            $to_save->set_name($raw_name);     // puis on refresh la page..      
            $this->redirect("category", "index");
        } else {
            // echec.. affiche la page avec les erreurs
            (new View("manage_categories"))->show([
                "categories" => Category::get_categories(),
                "save_errors" => $errors,
                "target_id" => $id
            ]);
        }
    }

    public function delete(int $id): void {
        $success = Category::delete($id);

        // on reload la page
        $this->redirect("category", "index");
    }

    public function add_category(): void {
        // on récupère l'input
        $raw_name = $_POST['name'];

        // on check si l'input est valide
        $errors = Category::validate($raw_name);

        // si pas d'erreurs..
        if (empty($errors)) {
            // success..
            // on ajoute la catégorie dans la db..
            Category::add(trim($raw_name));     // puis on refresh la page..      
            $this->redirect("category", "index");  // puis on arrête la fonction
            exit();
        } else {
            // echec.. affiche la page avec les erreurs
            (new View("manage_categories"))->show([
                "categories" => Category::get_categories(),
                "insert_errors" => $errors
            ]);
        }
    }

    // FONCTIONS AJAX
    public function delete_category_service() {
        header('Content-Type: application/json');

        // on extrait l'id
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

        if ($id !== null) {
            $current_user = $this->get_user_or_false();

            // erreur : utilisateur non connecté
            if (!$current_user) {
                echo json_encode(["success" => false, "response" => "User session lost"]);
                return;
            }

            // on essaie de supprimer la categorie
            $delete_ok = Category::delete($id); // true : success / false : categorie non vide, impossible de supprimer
            $response = "";

            if ($delete_ok) {   // success 
                $response = "Succès !";
            } else {            // fail
                $response = "Erreur. Impossible de supprimer une catégorie non vide. Categorie ID: " . $id;
            }

            // on envoie la réponse de la requête 
            echo json_encode([
                "success" => $delete_ok,
                "response" => $response
            ]);
            return;
        }

        // on envoie la réponse de la requête 
        echo json_encode([
            "success" => false,
            "response" => 'ID categorie ' . $id . 'invalide',
        ]);
    }
}
