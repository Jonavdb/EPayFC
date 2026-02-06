<?php
require_once "framework/Configuration.php";

class ImageTools
{

    public static function upload_and_resize($file, $target_folder_name, $id)
    {

        //On récupère la configuration de dev.ini
        $config_path = "config/dev.ini";
        $config = parse_ini_file($config_path);
        $allowed = $config['upload_allowed_types'];
        $max_size = (int)$config['upload_max_size'];
        $main_dim = (int)$config['main_image_max_size'];
        $thumb_dim = (int)$config['thumbnail_max_size'];
        $tmp_name = $file['tmp_name'];
        $size     = $file['size'];

        // On vérifie que le fichier respecte bien les règles métiers
        if ($size > $max_size) return false;
        $image_info = getimagesize($tmp_name);
        if (!$image_info || !in_array($image_info['mime'], $allowed)) {
            return false;
        }

        // On prépare le dossier
        $folder = "uploads/" . $target_folder_name . "/" . $id . "/";
        // les paramètres passés à MKDIR sont : le chemin, les autorisations (tout le monde peut lire,écrire et exécuter) et la récursivité (création récursive)
        if (!file_exists($folder)) mkdir($folder, 0777, true);

        // On replace le nom des fichiers par un iduniq pour éviter les conflits
        $clean_name = uniqid("img_");
        $path_main = $folder . $clean_name . ".jpg";
        $path_thumb = $folder . $clean_name . "_thumbnail.jpg";

        // On Crée les images en appelant la fonction en dessous
        if (
            self::process($file['tmp_name'], $path_main, $main_dim) &&
            self::process($file['tmp_name'], $path_thumb, $thumb_dim)
        ) {
            return $path_main; // Succès ! On renvoie le chemin
        }
        return false;
    }

    // La création d'image grâce à GD
    private static function process($src, $dst, $max)
    {
        $info = getimagesize($src);
        if ($info['mime'] == 'image/jpeg') $img = imagecreatefromjpeg($src);
        elseif ($info['mime'] == 'image/png') $img = imagecreatefrompng($src);
        elseif ($info['mime'] == 'image/gif') $img = imagecreatefromgif($src);
        elseif ($info['mime'] == 'image/webp') $img = imagecreatefromwebp($src);
        else return false;

        $new_img = imagescale($img, $max);
        $success = imagejpeg($new_img, $dst, 85);

        return $success;


        // $w = imagesx($img);
        // $h = imagesy($img);
        // $ratio = $w / $h;
        // if ($w > $h) {
        //     $nw = $max;
        //     $nh = $max / $ratio;
        // } else {
        //     $nw = $max * $ratio;
        //     $nh = $max;
        // }

        // $new = imagecreatetruecolor($nw, $nh);
        // imagefill($new, 0, 0, imagecolorallocate($new, 255, 255, 255));
        // imagecopyresampled($new, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        // return imagejpeg($new, $dst, 85);
    }
}
