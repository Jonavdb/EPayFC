<?php
require_once 'framework/Controller.php';
require_once 'model/User.php';

class ControllerProfile extends Controller
{
    public function index(): void
    {
        // (new View("profile"))->show();
        $this->see_my_sales();
    }

    public function see_my_sales(): void
    {
        (new View("see_my_sales"))->show();
    }
}
