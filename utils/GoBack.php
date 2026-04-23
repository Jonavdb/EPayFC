<?php

class GoBack {
    public static function go_back(): string {
        $uri = $_SERVER['REQUEST_URI'];

        // Correction du strpos avec !== false
        if (
            strpos($uri, 'user/edit_profile') !== false ||
            strpos($uri, 'user/change_password') !== false ||
            strpos($uri, 'user/manage_profile_picture') !== false ||
            strpos($uri, 'item/sales') !== false ||
            strpos($uri, 'item/purchases') !== false
        ) {
            return 'user/profile';
        }

        $clean_uri = trim($uri, '/');
        $parts = explode('/', $clean_uri);

        $params_map = [
            'from-browse' => 'item/browse',
            'from-items' => 'item/list',
            'from-sales' => 'item/sales',
            'from-purchases' => 'item/purchases'
        ];

        $from = null;
        foreach ($params_map as $param => $target_url) {
            if (strpos($uri, $param) !== false) {
                $from = $param;

                $encoded_search = ''; // attrape la recherche encodée si elle suit le paramètre contextuel
                $param_index = array_search($param, $parts);

                if ($param_index !== false && isset($parts[$param_index + 1]) && !is_numeric($parts[$param_index + 1])) {
                    $encoded_search = "/" . $parts[$param_index + 1];
                }

                if (isset($parts[3]) && is_numeric($parts[3]) && !strpos($uri, 'item/open')) {
                    return "item/open/" . $parts[3] . "/" . $from . $encoded_search;
                }

                return $target_url . $encoded_search;
            }
        }
        return 'item/browse';
    }


    static function smart_href(string $target_url): string {
        $params_to_keep = ['from-browse', 'from-items', 'from-sales', 'from-purchases'];
        foreach ($params_to_keep as $param) {
            if (strpos($_SERVER['REQUEST_URI'], $param) !== false) {
                $separator = (strpos($target_url, '?') !== false) ? '&' : '/';
                return $target_url . $separator . $param;
            }
        }
        return $target_url;
    }
}
