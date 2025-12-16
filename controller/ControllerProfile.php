<?php
require_once 'framework/Controller.php';
require_once 'model/User.php';

class ControllerProfile extends Controller {
    public function index(): void {
        (new View("profile"))->show();
    }
}
