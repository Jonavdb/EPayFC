<?php

class GoBack {
    public static function go_back(): string {

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
        } elseif (strpos($referer, 'item/add') !== false) {
            $_SESSION['smart_back_link'] = 'item/browse';
        } elseif (strpos($referer, 'item/browse') !== false) {
            $_SESSION['smart_back_link'] = 'item/browse';
        } elseif (strpos($referer, 'item/list') !== false) {
            $_SESSION['smart_back_link'] = 'item/list';
        } elseif (strpos($referer, 'user/profile') !== false) {
            $_SESSION['smart_back_link'] = 'user/profile';
        }

        return $_SESSION['smart_back_link'] ?? 'item/browse';
    }

    // Itération 2

    /**
     * Permet d'encoder un string au format base64url, c'est-à-dire un format base64 dans lequel
     * les caractères '+' et '/' sont remplacés respectivement par '-' et '_', ce qui permet d'utiliser le
     * résultat dans une URL.
     * @param string $data Le string à encoder.
     * @return string Le string encodé.
     */
    private static function base64url_encode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Permet de décoder un string encodé au format base64url.
     * @param string $data Le string à décoder.
     * @return string Le string décodé.
     */
    private static function base64url_decode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }

    /**
     * Permet d'encoder une structure de donnée (par exemple un tableau associatif ou un objet) au format base64url.
     * @param mixed $data La structure de données à encoder.
     * @return string Le string résultant de l'encodage.
     */
    public static function url_safe_encode(mixed $data): string {
        return self::base64url_encode(gzcompress(json_encode($data), 9));
    }

    /**
     * Permet de décoder un string au format base64url.
     * @param string Le string à décoder.
     * @return mixed $data La structure de données décodée. 
     */
    public static function url_safe_decode(string $data): mixed {
        return json_decode(@gzuncompress(self::base64url_decode($data)), true, 512, JSON_OBJECT_AS_ARRAY);
    }
}
