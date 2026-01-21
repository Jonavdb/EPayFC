<?php

class GoBack {
    public static function go_back(): string {
        $back_link = "item/browse";

        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            $back_link = $_SERVER['HTTP_REFERER'];
        }

        return $back_link;
    }
}
