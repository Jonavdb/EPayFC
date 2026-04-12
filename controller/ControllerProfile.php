<?php
require_once 'framework/Controller.php';
require_once 'model/User.php';

class ControllerProfile extends Controller
{
    public function index(): void
    {
        $user = $this->get_user_or_redirect();
        $user = User::get_user_by_email($user->get_email());
        (new View("profile"))->show(["user" => $user, "is_guest" => !$this->user_logged()]);
    }
}
