
<?php

require_once __DIR__ . "/../config/database.php";

class User {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Check whether email already exists
    public function emailExists($email) {

        $sql = "SELECT id FROM users WHERE email = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([$email]);

        return $stmt->fetch() !== false;
    }

    // Register new user
    public function register($name, $email, $password) {

        // Hash password
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $sql = "INSERT INTO users
                (name, email, password)
                VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $name,
            $email,
            $hashedPassword
        ]);
    }
}

?>
