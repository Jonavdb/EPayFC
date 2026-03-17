<?php
require_once 'framework/Controller.php';
require_once 'model/User.php';

class ControllerMain extends Controller {
    public function index(): void {
        if ($this->user_logged()) {
            $this->redirect("profile", "index");
        } else {
            (new View("signup"))->show();
        }
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
}
