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

    public function handleAction(): void {
        /*$is_empty_input = (isset($_POST['name']) || empty(trim($_POST['name'])));

        if ($is_input_empty) {
            // erreur : champ vide
            $error_empty_input = "Champ vide.";
            $category_name = $_POST['id'];

            (new View("manage_categories"))->show([
                "categories" => Category::get_categories(),
                "error_empty_field" => $error_empty_input,
                "target" => $category_name
            ]);
        }*/

        // on récupère l'action déclenchée, et on transfère la categorie cible à cette action
    }


    // Up
    public function Up(): void {
        // on récupère la priorité de la categorie (up) et on la stock dans une variable temp

        // on récupère la priorité de la categorie qui a la priorité la plus basse après la categorie (up)..
        // on la stock dans une variable temp

        // on met la priorité de la categorie qui a la priorité la plus basse après la categorie (up) à -1

        // on met la priorité de la categorie (up) a la priorité de l'autre avec son temp

        // on met la priorité de l'autre a la priorité de la categorie (up) avec son temp (up)

        // on reload la page
    }

    // Down
    public function Down(): void {
    }

    // Save
    public function Save(): void {
    }

    // Delete
    public function Delete(): void {
    }

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
