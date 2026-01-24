<?php

require_once './framework/Model.php';

class User extends Model
{
    public ?int $id;
    public string $email;
    public string $full_name;
    public string $pseudo;
    public string $password;
    public ?string $picture_path;
    public ?string $iban;
    public string $role;

    public function __construct(
        string $email,
        string $full_name,
        string $pseudo,
        string $password,
        ?int $id = null,
        ?string $picture_path = null,
        ?string $iban = null,
        string $role = "user"
    ) {
        $this->email = $email;
        $this->full_name = $full_name;
        $this->pseudo = $pseudo;
        $this->password = $password;
        $this->id = $id;
        $this->picture_path = $picture_path;
        $this->iban = $iban;
        $this->role = $role;
    }

    // Récupère les utilisateurs pour la liste de debug
    public static function get_debug_users(): array
    {
        $query = self::execute("SELECT * FROM users WHERE pseudo IN ('quentin', 'boris', 'xavier', 'marc')", []);
        $data = $query->fetchAll();
        $users = [];

        foreach ($data as $row) {
            $users[] = new User(
                $row['email'],
                $row['full_name'],
                $row['pseudo'],
                $row['password'],
                $row['id']
            );
        }
        return $users;
    }

    // récupère le user par email
    public static function get_user_by_email(string $email): User|false
    {
        $query = self::execute("SELECT * FROM Users where email = :email", ["email" => $email]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User(
                $data["email"],
                $data["full_name"],
                $data["pseudo"],
                $data["password"], // On passe le hash de la DB dans la propriété password
                $data["id"] ?? null,
                $data["picture_path"],
                $data["role"]
            );
        }
    }

    public static function validate_login(string $email, string $password): array
    {
        $errors = [];
        $user = User::get_user_by_email($email);
        if ($user) {
            if (!self::check_password($password, $user->password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a user with the email '$email'. Please sign up.";
        }
        return $errors;
    }

    public function validate(): array
    {
        $errors = [];
        if (!strlen($this->pseudo) > 0) {
            $errors[] = "Pseudo is required.";
        }
        if (!(strlen($this->pseudo) >= 3 && strlen($this->pseudo) <= 16)) {
            $errors[] = "Pseudo length must be between 3 and 16.";
        }
        if (!(preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/", $this->pseudo))) {
            $errors[] = "Pseudo must start by a letter and must contain only letters and numbers.";
        }
        return $errors;
    }

    public static function validate_unicity(string $pseudo): array
    {
        $errors = [];
        $user = self::get_user_by_pseudo($pseudo);
        if ($user) {
            $errors[] = "This user already exists.";
        }
        return $errors;
    }

    public static function get_user_by_pseudo(string $pseudo): User|false
    {
        $query = self::execute("SELECT * FROM users where pseudo = :pseudo", ["pseudo" => $pseudo]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User(
                $data["email"],
                $data["full_name"],
                $data["pseudo"],
                $data["password"],
                $data["id"] ?? null
            );
        }
    }

    public function persist(): User
    {
        if (self::get_user_by_email($this->email)) {
            self::execute(
                "UPDATE users SET password=:password, full_name=:full_name, pseudo=:pseudo WHERE email=:email",
                [
                    "password" => $this->password,
                    "full_name" => $this->full_name,
                    "email" => $this->email,
                    "pseudo" => $this->pseudo
                ]
            );
        } else {
            self::execute(
                "INSERT INTO users(email, full_name, pseudo, password) VALUES(:email, :full_name, :pseudo, :password)",
                [
                    "email" => $this->email,
                    "full_name" => $this->full_name,
                    "pseudo" => $this->pseudo,
                    "password" => $this->password
                ]
            );
        }
        return $this;
    }

    private static function check_password(string $clear_password, string $hash): bool
    {
        return password_verify($clear_password, $hash);
    }

    public static function validate_passwords(string $password, string $password_confirm): array
    {
        $errors = User::validate_password($password);
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        return $errors;
    }

    private static function validate_password(string $password): array
    {
        $errors = [];
        if (strlen($password) < 8 || strlen($password) > 16) {
            $errors[] = "Password length must be between 8 and 16.";
        }
        if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?!\\-]/", $password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }
}
