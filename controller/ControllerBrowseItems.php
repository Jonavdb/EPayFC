<?php
require_once "framework/Controller.php";

class ControllerBrowseItems extends Controller {
    
    public function index(): void {
        $this->show_items();
    }

    public function show_items(): void {
        (new View("browse_items"))->show();
    }
}