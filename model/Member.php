<?php

class Member extends Model {
    public function __construct(public string $pseudo, public string $hashed_password, public ?string $profile = null, public ?string $picture_path = null) {
    }

    // récupère le membre par email
    public static function get_member_by_email(string $email): Member|false {
        $query = self::execute("SELECT * FROM Members where email = :email", ["email" => $email]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Member($data["email"], $data["pseudo"], $data["password"], $data["full_name"]);
        }
    }

    public static function validate_login(string $email, string $password): array {
        $errors = [];
        $member = Member::get_member_by_email($email);
        if ($member) {
            if (!self::check_password($password, $member->hashed_password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a member with the email '$email'. Please sign up.";
        }
        return $errors;
    }

    private static function check_password(string $clear_password, string $hash): bool {
        return password_verify($clear_password, $hash);
    }
}
