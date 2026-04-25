<?php

require_once './framework/Model.php';

class User extends Model {
    private ?int $id;
    private string $email;
    private string $full_name;
    private string $pseudo;
    private string $password;
    private ?string $picture_path;
    private ?string $iban;
    private string $role;

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
        $this->email        = $email;
        $this->full_name    = $full_name;
        $this->pseudo       = $pseudo;
        $this->password     = $password;
        $this->id           = $id;
        $this->picture_path = $picture_path;
        $this->iban         = $iban;
        $this->role         = $role;
    }

    public function get_id(): ?int {
        return $this->id;
    }
    public function get_email(): string {
        return $this->email;
    }
    public function get_full_name(): string {
        return $this->full_name;
    }
    public function get_pseudo(): string {
        return $this->pseudo;
    }
    public function get_password(): string {
        return $this->password;
    }
    public function get_picture_path(): ?string {
        return $this->picture_path;
    }
    public function get_iban(): ?string {
        return $this->iban;
    }
    public function get_role(): string {
        return $this->role;
    }

    public function set_id(int $id): void {
        $this->id = $id;
    }
    public function set_password(string $password): void {
        $this->password = $password;
    }
    public function set_picture_path(?string $path): void {
        $this->picture_path = $path;
    }
    public function set_full_name(string $full_name): void {
        $this->full_name = $full_name;
    }
    public function set_pseudo(string $pseudo): void {
        $this->pseudo = $pseudo;
    }
    public function set_email(string $email): void {
        $this->email = $email;
    }
    public function set_iban(?string $iban): void {
        $this->iban = $iban;
    }


    // Récupère les utilisateurs pour la liste de debug
    public static function get_debug_users(): array {
        $query = self::execute("SELECT * FROM users LIMIT 4", []);
        $data = $query->fetchAll();
        $users = [];

        foreach ($data as $row) {
            $users[] = new User(
                $row['email'],
                $row['full_name'],
                $row['pseudo'],
                $row['password'],
                $row['id'],
                $row['picture_path'],
                $row['iban'],
                $row['role']
            );
        }
        return $users;
    }

    // récupère le user par full_name
    public static function get_user_by_full_name(string $full_name): User|false {
        $query = self::execute("SELECT * FROM users where full_name = :full_name", ["full_name" => $full_name]);
        $data = $query->fetch(); // un seul résultat au maximum
        if (!$data) {
            return false;
        } else {
            return new User(
                $data["email"],
                $data["full_name"],
                $data["pseudo"],
                $data["password"],
                $data["id"] ?? null,
                $data["picture_path"],
                $data["iban"],
                $data["role"]
            );
        }
    }

    // récupère le user par email
    public static function get_user_by_email(string $email): User|false {
        $query = self::execute("SELECT * FROM users where email = :email", ["email" => $email]);
        $data = $query->fetch(); // un seul résultat au maximum
        if (!$data) {
            return false;
        } else {
            return new User(
                $data["email"],
                $data["full_name"],
                $data["pseudo"],
                $data["password"], // On passe le hash de la DB dans la propriété password
                $data["id"] ?? null,
                $data["picture_path"],
                $data["iban"],
                $data["role"]
            );
        }
    }

    public static function validate_login(string $email, string $password): array {
        $errors = [];
        $user = User::get_user_by_email($email);
        if ($user) {
            if (!self::check_password($password, $user->get_password())) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a user with the email '$email'. Please sign up.";
        }
        return $errors;
    }

    public function validate(): array {
        $errors = [];

        $min_fullname_len = Configuration::get('full_name_min_length');
        $min_pseudo_len   = Configuration::get('pseudo_min_length');
        $max_pseudo_len   = Configuration::get('pseudo_max_length');
        $iban_regex       = Configuration::get('iban_regex');
        $iban_format      = Configuration::get('iban_format');

        // Full Name
        if (strlen($this->full_name) < $min_fullname_len) {
            $errors['full_name'] = "Le nom complet doit comporter au moins $min_fullname_len caractères.";
        }

        // Email
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email_format'] = "Le format de l'adresse email n'est pas valide.";
        } else {
            // On utilise 'self::' puisqu'on est déjà dans la classe User
            $existing_user = self::get_user_by_email($this->email);
            if ($existing_user && $existing_user->id !== $this->id) {
                $errors['email_already_used'] = "Cette adresse email est déjà utilisée.";
            }
        }

        // Pseudo
        if (strlen($this->pseudo) < $min_pseudo_len || strlen($this->pseudo) > $max_pseudo_len) {
            $errors['pseudo_length'] = "Le pseudo doit avoir une longueur comprise entre $min_pseudo_len et $max_pseudo_len caractères.";
        } else {
            $existing_user = self::get_user_by_pseudo($this->pseudo);
            if ($existing_user && $existing_user->id !== $this->id) {
                $errors['pseudo_already_used'] = "Ce pseudo est déjà utilisé.";
            }
        }

        // IBAN
        if (!empty($this->iban)) {
            if (!preg_match($iban_regex, $this->iban)) {
                $errors['iban'] = "L'IBAN doit respecter le format : $iban_format.";
            }
        }

        return $errors;
    }

    public static function validate_unicity(string $pseudo): array {
        $errors = [];
        $user = self::get_user_by_pseudo($pseudo);
        if ($user) {
            $errors['user_already_exists'] = "This user already exists.";
        }
        return $errors;
    }

    public static function get_user_by_pseudo(string $pseudo): User|false {
        $query = self::execute("SELECT * FROM users where pseudo = :pseudo", ["pseudo" => $pseudo]);
        $data = $query->fetch(); // un seul résultat au maximum
        if (!$data) {
            return false;
        } else {
            return new User(
                $data["email"],
                $data["full_name"],
                $data["pseudo"],
                $data["password"],
                $data["id"] ?? null,
                $data["picture_path"],
                $data["iban"],
                $data["role"]
            );
        }
    }

    public function persist(): User {
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
            $this->id = (int)self::lastInsertId();
        }
        return $this;
    }

    private static function check_password(string $clear_password, string $hash): bool {
        return password_verify($clear_password, $hash);
    }

    public static function validate_passwords(string $password, string $password_confirm): array {
        $errors = User::validate_password($password);
        if ($password != $password_confirm) {
            $errors['same_password'] = "You have to enter twice the same password.";
        }
        return $errors;
    }

    private static function validate_password(string $password): array {
        $errors = [];
        $min_pwd = Configuration::get('password_min_length');
        $max_pwd = Configuration::get('password_max_length');

        if (strlen($password) < $min_pwd || strlen($password) > $max_pwd) {
            $errors['password_length'] = "Password length must be between $min_pwd and $max_pwd.";
        }
        if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?!\\-]/", $password))) {
            $errors['password_retry'] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }

    public function update(): User {
        if ($this->id) {
            self::execute(
                "UPDATE users SET 
                email = :email, 
                full_name = :full_name, 
                pseudo = :pseudo, 
                password = :password, 
                picture_path = :picture_path, 
                iban = :iban, 
                role = :role 
             WHERE id = :id",
                [
                    "id" => $this->id,
                    "email" => $this->email,
                    "full_name" => $this->full_name,
                    "pseudo" => $this->pseudo,
                    "password" => $this->password,
                    "picture_path" => $this->picture_path,
                    "iban" => $this->iban,
                    "role" => $this->role
                ]
            );
        }
        return $this;
    }
}
