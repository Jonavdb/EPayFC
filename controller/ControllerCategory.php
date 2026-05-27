<?php
require_once "framework/Controller.php";
require_once "Model/Category.php";
require_once "Model/User.php";


class ControllerCategory extends Controller {
    public function index(): void {
        $this->redirect("category", "manage");
    }

    // Manage
    public function manage(): void {

        // On récupère la liste des catégories actuelles
        $categories = Category::get_categories();
        $user = $this->get_user_or_false();
        $user_role = $user->get_role();
        // On les transfère à la vue
        (new View("manage_categories"))->show([
            "categories" => $categories,
            "user_role" => $user_role
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
        $raw_name = $_POST['category_name'];

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
        $user = $this->get_user_or_false();
        $user_role = $user->get_role();
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
                "target_id" => $id,
                "user_role" => $user_role
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
        $user = $this->get_user_or_false();
        $user_role = $user->get_role();

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
                "insert_errors" => $errors,
                "user_role" => $user_role
            ]);
        }
    }

    // SERVICE AJAX
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

    public function update_order_categories_service() {
        header('Content-Type: application/json');

        // on extrait la liste et on force chaque élément à devenir un entier (int) pour la comparaison stricte
        $newOrder = !empty($_POST['newOrder']) ? array_map('intval', (array)$_POST['newOrder']) : null;

        if ($newOrder !== null) {
            $current_user = $this->get_user_or_false();

            // erreur : utilisateur non connecté
            if (!$current_user) {
                echo json_encode(["success" => false, "response" => "User session lost"]);
                return;
            }


            // Vu que la fonction retourne maintenant directement la liste des IDs fraîchement triés, 
            // on l'exécute et on stocke directement le résultat dans $savedOrder
            $savedOrder = Category::update_order($newOrder);

            // comparaison stricte : est-ce que l'ordre demandé === l'ordre réellement sauvegardé ?
            $is_sync_ok = ($newOrder === $savedOrder);

            if ($is_sync_ok) {   // success 
                $response = "Succès !";
            } else {            // fail
                $response = "Erreur. Synchronisation base de données échouée";
            }

            // on envoie la réponse de la requête 
            echo json_encode([
                "success" => $is_sync_ok,
                "response" => $response
            ]);
            return;
        }

        // erreur : tableau vide ou inexistant
        echo json_encode([
            "success" => false,
            "response" => 'Liste ID categories invalide',
        ]);
    }

    public function check_unique_service() {
        header('Content-Type: application/json');

        // 1. THE WHITESPACE FIX: Use isset, cast to string, and immediately trim() spaces
        $name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';

        // 2. Check if the string is still not empty AFTER trimming
        if ($name !== '') {
            $current_user = $this->get_user_or_false();

            // erreur : utilisateur non connecté
            if (!$current_user) {
                echo json_encode(["success" => false, "response" => "User session lost"]);
                return;
            }

            // On cherche un duplicata
            $duplicate = Category::get_category_from_name($name);

            // 3. THE SAFE CHECK: Ensure it works whether your DB returns null OR false when empty
            $is_unique = $duplicate === null;

            if ($is_unique) {   // success 
                $response = "Succès !";
            } else {            // fail
                $response = "Cette catégorie existe déjà";
            }

            // on envoie la réponse de la requête 
            echo json_encode([
                "success" => $is_unique,
                "response" => $response
            ]);
            return;
        }

        // erreur : pas de nom reçu, ou l'utilisateur n'a tapé que des espaces
        echo json_encode([
            "success" => false,
            "response" => 'Nom vide ou invalide',
        ]);
    }

    public function save_name_service() {
        header('Content-Type: application/json');

        // 1. FIXED: Two separate variables!
        $new_name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;

        // 2. CHECK BOTH: Ensure name is not empty AND id is valid
        if ($new_name !== '' && $id !== null) {
            $current_user = $this->get_user_or_false();

            if (!$current_user) {
                echo json_encode(["success" => false, "response" => "User session lost"]);
                return;
            }

            $category = Category::get_category_from_id($id);

            // Sécurité : si la catégorie n'existe pas avec cet ID
            if (!$category) {
                echo json_encode(["success" => false, "response" => "Catégorie introuvable"]);
                return;
            }

            $category->set_name($new_name);

            $save_ok = ($category->get_name() === $new_name);

            if ($save_ok) {   // success 
                $response = "Sauvegarde effectuée !";
            } else {          // fail
                $response = "Sauvegarde non effectuée";
            }

            echo json_encode([
                "success" => $save_ok,
                "response" => $response
            ]);
            return;
        }

        // erreur globale
        echo json_encode([
            "success" => false,
            "response" => 'Données invalides ou manquantes',
        ]);
    }

    public function add_category_service() {
        header('Content-Type: application/json');

        // 1. FIXED: Two separate variables!
        $new_name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';

        // 2. CHECK BOTH: Ensure name is not empty AND id is valid
        if ($new_name !== '') {
            $current_user = $this->get_user_or_false();

            if (!$current_user) {
                echo json_encode(["success" => false, "response" => "User session lost"]);
                return;
            }

            $category = Category::get_category_from_name($new_name);

            // Sécurité : si la catégorie existe avec ce nom
            if ($category) {
                echo json_encode(["success" => false, "response" => "Cette catégorie existe déjà."]);
                return;
            }

            $new_category = Category::add($new_name);

            if ($new_category) {
                echo json_encode([
                    "success" => true,
                    "response" => "Catégorie ajoutée avec succès !",
                    "new_id" => $new_category->get_id() // optionnel : on envoie l'id de la nouvelle categorie pour créer une nouvelle rangée dynamiquement
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "response" => "Erreur lors de la sauvegarde en base de données."
                ]);
            }
            return;
        }

        // erreur globale
        echo json_encode([
            "success" => false,
            "response" => 'Données invalides ou manquantes',
        ]);
    }
}
