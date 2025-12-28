<?php
require_once 'framework/Controller.php';
require_once 'model/User.php';
require_once 'model/Profile.php';

class ControllerProfile extends Controller
{
    public function index(): void
    {
        // (new View("profile"))->show();
        $this->see_my_sales();
    }

    public function see_my_sales(): void
    {
        $sales = Profile::get_sales("Boris");
        (new View("see_my_sales"))->show(
            [
                "sales" => $sales
            ]
        );
    }
}
