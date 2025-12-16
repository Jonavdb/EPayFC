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

        if (
            isset($_POST['email'])
            && isset($_POST['pseudo'])
            && isset($_POST['full_name'])
            && isset($_POST['password'])
            && isset($_POST['password_confirm'])
        ) {
            $email = trim($_POST['email']);
            $full_name = trim($_POST['full_name']);
            $pseudo = trim($_POST['pseudo']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            $user = new User($email, $full_name, $pseudo, password_hash($password, PASSWORD_BCRYPT));
            $errors = User::validate_unicity($email);
            $errors = array_merge($errors, $user->validate());
            $errors = array_merge($errors, User::validate_passwords($password, $password_confirm));

            if (count($errors) == 0) {
                $user->persist(); //sauve l'utilisateur
                $this->log_user($user);
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
