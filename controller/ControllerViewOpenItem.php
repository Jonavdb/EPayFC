<?php

require_once "framework/Controller.php";

class ControllerViewOpenItem extends Controller {

    public function index(): void {
        if (!Configuration::is_dev()) {
            $this->redirect();
        }

        (new View("open_item"))->show();
    }
}
?>