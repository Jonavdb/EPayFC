<?php
require_once "framework/Controller.php";


class ControllerCategory extends Controller {
    public function index(): void {
        $this->redirect("category", "manage");
    }

    // Manage
    public function manage(): void {
        // Récup la liste des categories

        // Les transférer à la vue
        // Debug : on affiche juste la vue pour l'instant
        (new View("manage_categories"))->show([]);
    }

    // Edit 

    // Up

    // Down

    // Save

    // Delete

    // Add Category
}
