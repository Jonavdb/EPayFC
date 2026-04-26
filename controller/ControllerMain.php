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
}
