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

    // Edit 

    // Up

    // Down

    // Save

    // Delete

    // Add Category
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
