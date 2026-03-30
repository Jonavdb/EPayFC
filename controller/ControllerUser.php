<?php
require_once 'framework/Controller.php';
require_once 'model/User.php';
require_once 'utils/ImageTool.php';

class ControllerUser extends Controller {
    public function index(): void {
        if ($this->user_logged()) {
            $this->redirect("item", "browse");
        } else {
            $this->redirect("user", "login");
        }
    }

    // Connexion en tant qu'invité
    public function guest_login(): void {
        session_destroy(); // kill la session active

        $_SESSION['guest'] = true;
        $this->redirect("item", "browse");
    }

    public function profile(): void {
        $user = $this->get_user_or_redirect();
        $user = User::get_user_by_email($user->get_email());
        (new View("profile"))->show(["user" => $user]);
    }

    //gestion de l'inscription d'un utilisateur
    public function signup(): void {
        $email = '';
        $full_name = '';
        $pseudo = '';
        $password = '';
        $password_confirm = '';
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
            $pseudo = isset($_POST['pseudo']) ? trim($_POST['pseudo']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

            $user = new User($email, $full_name, $pseudo, password_hash($password, PASSWORD_BCRYPT));


            $errors = $user->validate();
            $errors = array_merge($errors, User::validate_passwords($password, $password_confirm));

            if (empty($errors)) {
                $user->persist();
                $this->log_user($user);

                $this->redirect("item", "browse");
                return;
            }
        }

        (new View("signup"))->show([
            "email" => $email,
            "full_name" => $full_name,
            "pseudo" => $pseudo,
            "password" => $password,
            "password_confirm" => $password_confirm,
            "errors" => $errors
        ]);
    }

    // Gestion de la connexion d'un utilisateur
    public function login(): void {
        $email = '';
        $password = '';
        $errors = [];

        // GESTION DU LOGIN AUTOMATIQUE VIA LIEN DE DEBUG
        if (isset($_GET['email'])) {
            // Si l'URL contient ?email=..., on connecte direct
            $user = User::get_user_by_email($_GET['email']);

            if ($user) {
                $this->log_user($user);
                $this->redirect("profile", "index");
            }
        }

        // GESTION DU LOGIN CLASSIQUE (POST)
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $errors = User::validate_login($email, $password);

            if (empty($errors)) {
                $this->log_user(User::get_user_by_email($email));
                $this->redirect("profile", "index");
            }
        }

        // RÉCUPÉRATION DE LA LISTE DES "Debug Users"
        $users = User::get_debug_users();

        (new View("login"))->show(["email" => $email, "password" => $password, "errors" => $errors, "users" => $users]);
    }

    // Gestion du changement de mot de passe
    public function change_password(): void {
        $session_user = $this->get_user_or_false();

        if (!$session_user) {
            $this->redirect("user", "login");
        }

        // Recharge depuis la DB pour avoir le hash à jour (évite des bugs)
        $user = User::get_user_by_email($session_user->get_email());

        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['new_password_confirm'] ?? '';
        $errors = [];

        if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['new_password_confirm'])) {
            // Vérification current password
            if (!password_verify($current_password, $user->get_password())) {
                $errors['current_password'] = "Current password is incorrect.";
            }

            // Validation new password
            $errors = array_merge($errors, User::validate_passwords($new_password, $confirm_password));

            if (empty($errors)) {
                $user->set_password(password_hash($new_password, PASSWORD_BCRYPT));
                $user->update();
                $this->redirect("user", "profile");
            }
        }

        (new View("change_password"))->show([
            "errors" => $errors,
            "current_password" => $current_password,
            "new_password" => $new_password,
            "confirm_password" => $confirm_password
        ]);
    }

    // Gère l'édition du profil
    public function edit_profile(): void {
        $user = $this->get_user_or_redirect();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user->set_full_name(trim($_POST['full_name'] ?? ''));
            $user->set_pseudo(trim($_POST['pseudo'] ?? ''));
            $user->set_email(trim($_POST['email'] ?? ''));
            $user->set_iban(trim($_POST['iban'] ?? ''));

            $errors = $user->validate();

            if (empty($errors)) {
                $user->update();
                $this->redirect("user", "profile");
            }
        }

        (new View("edit_profile"))->show([
            "user" => $user,
            "errors" => $errors
        ]);
    }

    public function manage_profile_picture() {

        $user = $this->get_user_or_redirect();

        (new View("profile_picture"))->show(["user" => $user]);
    }

    public function update_profile_picture() {
        $user = $this->get_user_or_redirect();
        $config_path = "config/dev.ini";
        $config = parse_ini_file($config_path);
        $max_size = (int)$config['upload_max_size'];
        $allowed_type = $config['upload_allowed_types'];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
            $file = $_FILES['profile_pic'];
            $tmp_name = $file['tmp_name'];
            $name = $file['name'];
            $size = $file['size'];
            $error = $file['error'];

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

                    $path = ImageTools::upload_and_resize($file, "users", $user->get_id());
                    if ($path) {
                        if (!empty($user->get_picture_path()) && file_exists($user->get_picture_path())) {
                            unlink($user->get_picture_path());
                            $old_thumb = str_replace(".jpg", "_thumbnail.jpg", $user->get_picture_path());
                            if (file_exists($old_thumb)) {
                                unlink($old_thumb);
                            }
                        }

                        $user->set_picture_path($path);
                        $user->update();
                    }
                }
            }
        }
        if (count($errors) === 0) {
            $this->redirect("user", "manage_profile_picture");
        } else {
            (new View("profile_picture"))->show([
                "user" => $user,
                "errors" => $errors
            ]);
        }
    }

    public function delete_profile_picture() {
        $user = $this->get_user_or_redirect();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($user->get_picture_path()) && file_exists($user->get_picture_path())) {
                unlink($user->get_picture_path());
                $old_thumb = str_replace(".jpg", "_thumbnail.jpg", $user->get_picture_path());
                if (file_exists($old_thumb)) {
                    unlink($old_thumb);
                }
            }
            $user->set_picture_path(null);
            $user->update();
            $this->redirect("user", "manage_profile_picture");
        }
    }

    // Services AJAX
    public function full_name_exists_service(): void {
        $exists = false;

        if (isset($_GET["full_name"]) && $_GET["full_name"] !== "") {
            $full_name = trim($_GET["full_name"]);
            $full_name_in_db = User::get_user_by_full_name($full_name);
            $current_user = $this->get_user_or_false();

            if ($full_name_in_db && $full_name_in_db->get_full_name() !== $current_user->get_full_name()) {
                $exists = true;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(["exists" => $exists]);
    }

    public function pseudo_exists_service(): void {
        $exists = false;

        if (isset($_GET["pseudo"]) && trim($_GET["pseudo"]) !== "") {
            $pseudo = trim($_GET["pseudo"]);
            $pseudo_in_db = User::get_user_by_pseudo($pseudo);
            $current_user = $this->get_user_or_false();

            if ($pseudo_in_db && $pseudo_in_db->get_pseudo() !== $current_user->get_pseudo()) {
                $exists = true;
            }
        }
        header('Content-Type: application/json');
        echo json_encode(["exists" => $exists]);
    }

    public function email_exists_service(): void {
        $exists = false;

        if (isset($_POST["email"]) && trim($_POST["email"]) !== "") {
            $email = trim($_POST["email"]);
            $user_in_db = User::get_user_by_email($email);
            $current_user = $this->get_user_or_false();

            if ($user_in_db && $user_in_db->get_id() !== $current_user->get_id()) {
                $exists = true;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(["exists" => $exists]);
    }
}
