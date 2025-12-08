<?php
require_once 'framework/Controller.php';
require_once 'model/User.php';

class ControllerUser extends Controller {
    // TODO
    public function index(): void {
        // if ($this->user_logged()) {
        //     $this->redirect("user", "profile");
        // } else {
        //     (new View("index"))->show();
        // }
        var_dump("mot de passe changé bb")(new View("index"))->show();
    }

    //gestion de la connexion d'un utilisateur
    public function login(): void {
        $email = '';
        $password = '';
        $errors = [];

        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $errors = User::validate_login($email, $password);
            if (empty($errors)) {
                $this->log_user(User::get_user_by_email($email));
            }
        }
        (new View("login"))->show(["email" => $email, "password" => $password, "errors" => $errors]);
    }

    // Gestion du changement de mot de passe
    public function change_password(): void {
        // $errors = [];

        // if (isset($_POST['btnChangedPwd'])) {
        //     $new_password = $_POST['new_password'];
        //     $new_password_confirm = $_POST['new_password_confirm'];
        // }

        // $errors = User::validate_passwords($new_password, $new_password_confirm);
        // if (empty($errors)) {
        //     $this->index();
        // }

        (new View("change_password"))->show();
    }
}
