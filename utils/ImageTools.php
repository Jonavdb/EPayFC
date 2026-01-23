<?php
require_once "framework/Configuration.php";

class ImageTools
{

    // C'est cette fonction que tu appelleras. Elle gère TOUT.
    public static function upload_and_resize($file, $target_folder_name, $id)
    {

        // A. On charge les règles du fichier .ini
        $allowed = Configuration::get("upload_allowed_types");
        $max_size = Configuration::get("upload_max_size");
        $main_dim = Configuration::get("main_image_max_size");
        $thumb_dim = Configuration::get("thumbnail_max_size");

        // B. Vérifications Sécurité (Taille et Type)
        if ($file['size'] > $max_size) return false;

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $allowed)) return false;

        // C. Préparation du dossier (ex: uploads/items/42/)
        $folder = "uploads/" . $target_folder_name . "/" . $id . "/";
        if (!file_exists($folder)) mkdir($folder, 0777, true);

        // D. Noms de fichiers
        $filename = pathinfo($file['name'], PATHINFO_FILENAME);
        $clean_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $filename);
        $path_main = $folder . $clean_name . ".jpg";
        $path_thumb = $folder . $clean_name . "_thumbnail.jpg";

        // E. Création des images (Appel à la fonction technique en bas)
        if (
            self::process($file['tmp_name'], $path_main, $main_dim) &&
            self::process($file['tmp_name'], $path_thumb, $thumb_dim)
        ) {
            return $path_main; // Succès ! On renvoie le chemin
        }
        return false;
    }

    // La "cuisine" interne GD (Ne t'embête pas à lire ça)
    private static function process($src, $dst, $max)
    {
        $info = getimagesize($src);
        if ($info['mime'] == 'image/jpeg') $img = imagecreatefromjpeg($src);
        elseif ($info['mime'] == 'image/png') $img = imagecreatefrompng($src);
        elseif ($info['mime'] == 'image/gif') $img = imagecreatefromgif($src);
        elseif ($info['mime'] == 'image/webp') $img = imagecreatefromwebp($src);
        else return false;

        $w = imagesx($img);
        $h = imagesy($img);
        $ratio = $w / $h;
        if ($w > $h) {
            $nw = $max;
            $nh = $max / $ratio;
        } else {
            $nw = $max * $ratio;
            $nh = $max;
        }

        $new = imagecreatetruecolor($nw, $nh);
        imagefill($new, 0, 0, imagecolorallocate($new, 255, 255, 255));
        imagecopyresampled($new, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        return imagejpeg($new, $dst, 85);
    }
}
