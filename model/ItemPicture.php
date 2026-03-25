<?php

require_once "framework/Model.php";

class ItemPicture extends Model
{
    private int $item_id;
    private string $picture_path;
    private string $thumbnail_path;
    private int $priority;

    public function __construct(int $item_id, string $picture_path, int $priority)
    {
        $this->item_id      = $item_id;
        $this->picture_path = $picture_path;
        $this->priority     = $priority;
        $path_parts = pathinfo($picture_path);
        if (isset($path_parts['dirname'], $path_parts['filename'], $path_parts['extension'])) {
            $this->thumbnail_path = $path_parts['dirname'] . '/' .
                $path_parts['filename'] . '_thumbnail.' .
                $path_parts['extension'];
        } else {
            $this->thumbnail_path = $picture_path;
        }
    }

    public function get_item_id(): int
    {
        return $this->item_id;
    }

    public function get_picture_path(): string
    {
        return $this->picture_path;
    }

    public function get_thumbnail_path(): string
    {
        return $this->thumbnail_path;
    }

    public function get_priority(): int
    {
        return $this->priority;
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->picture_path)) {
            $errors[] = "Le chemin de l'image ne peut pas être vide.";
        }

        if ($this->priority < 0) {
            $errors[] = "La priorité de l'image doit être un entier positif ou nul.";
        }

        return $errors;
    }

    public static function get_pictures_by_item(int $item_id): array
    {
        $query = self::execute(
            "SELECT picture_path, priority
             FROM item_pictures
             WHERE item = :item_id
             ORDER BY priority ASC",
            ["item_id" => $item_id]
        );

        $pictures = [];
        foreach ($query->fetchAll() as $row) {
            $pictures[] = new ItemPicture($item_id, $row['picture_path'], (int)$row['priority']);
        }
        return $pictures;
    }
}
