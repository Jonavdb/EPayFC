<?php
require_once 'framework/Controller.php';

class ControllerUser extends Controller {
    public function index(): void {
        (new View("login"))->show();
    }
}
