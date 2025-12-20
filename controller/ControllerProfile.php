<?php
require_once 'framework/Controller.php';
require_once 'model/User.php';
require_once 'model/Profile.php';

class ControllerProfile extends Controller
{
    public function index(): void
    {
        //(new View("profile"))->show();
        $this->see_my_purchases();
    }

    public function see_my_purchases(): void
    {
        $my_purchases = Profile::get_purchases('Boris');
        (new View("see_my_purchases"))->show([
            "purchases" => $my_purchases
        ]);
    }
}
