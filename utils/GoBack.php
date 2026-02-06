<?php

class GoBack
{
    public static function go_back(): string
    {
        $back_link = "item/browse";

        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            $back_link = $_SERVER['HTTP_REFERER'];
        }

        return $back_link;
    }

    public static function go_back_2(): string
    {

        $uri = $_SERVER['REQUEST_URI'];
        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        if (
            strpos($uri, 'item/edit') !== false ||
            strpos($uri, 'item/manage_images') !== false ||
            strpos($uri, 'item/delete') !== false
        ) {

            $clean_uri = rtrim($uri, '/');

            $parts = explode('/', $clean_uri);

            $last_part = end($parts);

            if (is_numeric($last_part)) {
                return "item/open/$last_part";
            }
        }

        if (strpos($referer, 'item/open') !== false && strpos($uri, 'item/open') === false) {
            $path_part = strstr($referer, 'item/open');
            $parts = explode('/', $path_part);

            if (isset($parts[2]) && is_numeric($parts[2])) {
                return "item/open/" . $parts[2];
            }
        }

        if (strpos($uri, 'from-browse') !== false) {
            $_SESSION['smart_back_link'] = 'item/browse';
        } elseif (strpos($uri, 'from-list') !== false) {
            $_SESSION['smart_back_link'] = 'item/list';
        } elseif (strpos($uri, 'from-profile') !== false) {
            $_SESSION['smart_back_link'] = 'user/profile';
        } elseif (strpos($uri, 'from-sales') !== false) {
            $_SESSION['smart_back_link'] = 'item/sales';
        } elseif (strpos($uri, 'from-purchases') !== false) {
            $_SESSION['smart_back_link'] = 'item/purchases';
        } elseif (strpos($referer, 'item/browse') !== false) {
            $_SESSION['smart_back_link'] = 'item/browse';
        } elseif (strpos($referer, 'item/list') !== false) {
            $_SESSION['smart_back_link'] = 'item/list';
        } elseif (strpos($referer, 'user/profile') !== false) {
            $_SESSION['smart_back_link'] = 'user/profile';
        }

        return $_SESSION['smart_back_link'] ?? 'item/browse';
    }
}
