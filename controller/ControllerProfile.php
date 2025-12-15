<?php
require_once 'framework/Controller.php';
require_once 'model/User.php';

class ControllerProfile extends Controller
{
    public function index(): void
    {
        //(new View("profile"))->show();
        $this->see_my_purchases();
    }

    public function see_my_purchases(): void
    {
        (new View("see_my_purchases"))->show();
    }
}
