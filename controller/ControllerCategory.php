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
}
