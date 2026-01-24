<?php
require_once 'framework/Controller.php';
require_once 'model/User.php';
require_once 'utils/ImageTool.php';

class ControllerUser extends Controller
{
    public function index(): void
    {
        if ($this->user_logged()) {
            $this->redirect("item", "browse");
        } else {
            $this->redirect("user", "login");
        }
    }

    // Connexion en tant qu'invité
    public function guest_login(): void
    {
        session_destroy(); // kill la session active

        $_SESSION['guest'] = true;
        $this->redirect("browse_items", "index");
    }

    public function profile(): void
    {
        $user = $this->get_user_or_redirect();
        $user = User::get_user_by_email($user->email);
        (new View("profile"))->show(["user" => $user]);
    }

    // Gestion de la connexion d'un utilisateur
    public function login(): void
    {
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
    public function change_password(): void
    {
        $session_user = $this->get_user_or_false();

        if (!$session_user) {
            $this->redirect("user", "login");
        }

        // Recharge depuis la DB pour avoir le hash à jour (évite des bugs)
        $user = User::get_user_by_email($session_user->email);

        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['new_password_confirm'] ?? '';
        $errors = [];

        if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['new_password_confirm'])) {
            // Vérification current password
            if (!password_verify($current_password, $user->password)) {
                $errors[] = "Le mot de passe actuel est incorrect.";
            }

            // Validation new password
            $errors = array_merge($errors, User::validate_passwords($new_password, $confirm_password));

            if (empty($errors)) {
                $user->password = password_hash($new_password, PASSWORD_BCRYPT);
                $user->persist();
                $this->redirect("profile", "index");
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
    public function edit_profile(): void
    {
        $user = $this->get_user_or_redirect();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $full_name = trim($_POST['full_name'] ?? '');
            $pseudo = trim($_POST['pseudo'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $iban = trim($_POST['iban'] ?? '');

            // Validation
            if (empty($full_name)) $errors[] = "Full name is required.";
            if (empty($pseudo)) $errors[] = "Username is required.";
            if (empty($email)) $errors[] = "Email is required.";

            // Si pas d'erreurs, on sauvegarde
            if (empty($errors)) {
                $user->full_name = $full_name;
                $user->pseudo = $pseudo;
                $user->email = $email;
                $user->iban = $iban;

                $user->persist();
                $this->redirect("user", "profile");
            }
        }

        (new View("edit_profile"))->show([
            "user" => $user,
            "errors" => $errors
        ]);
    }

    public function manage_profile_picture()
    {

        $user = $this->get_user_or_redirect();

        (new View("profile_picture"))->show(["user" => $user]);
    }

    public function update_profile_picture()
    {
        $user = $this->get_user_or_redirect();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
            $file = $_FILES['profile_pic'];
            $path = ImageTools::upload_and_resize($file, "users", $user->id);
            if ($path) {
                if (!empty($user->picture_path) && file_exists($user->picture_path)) {
                    unlink($user->picture_path);
                    $old_thumb = str_replace(".jpg", "_thumbnail.jpg", $user->picture_path);
                    if (file_exists($old_thumb)) {
                        unlink($old_thumb);
                    }
                }

                $user->picture_path = $path;
                $user->update();
                $this->redirect("user", "manage_profile_picture");
            } else {
                $_SESSION['error'] = "Invalid file (max 2MB, JPG/PNG/WEBP only).";
                $this->redirect("user", "manage_profile_picture");
            }
        }
    }

    public function delete_profile_picture()
    {
        $user = $this->get_user_or_redirect();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($user->picture_path) && file_exists($user->picture_path)) {
                unlink($user->picture_path);
                $old_thumb = str_replace(".jpg", "_thumbnail.jpg", $user->picture_path);
                if (file_exists($old_thumb)) {
                    unlink($old_thumb);
                }
            }
            $user->picture_path = null;
            $user->update();
            $this->redirect("user", "manage_profile_picture");
        }
    }
}
