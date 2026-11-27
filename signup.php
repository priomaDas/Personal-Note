
<?php

session_start();

require_once "config/database.php";
require_once "classes/User.php";

// Database connection
$database = new Database();
$conn = $database->connect();

if (!$conn) {
    die("Something went wrong. Please try again later.");
}

// Create User object
$user = new User($conn);

$errors = [];
$success = "";

$name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form data
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    // Name validation
    if ($name === "") {
        $errors[] = "Name is required.";
    } elseif (strlen($name) > 100) {
        $errors[] = "Name is too long.";
    }

    // Email validation
    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address.";
    } elseif (strlen($email) > 150) {
        $errors[] = "Email is too long.";
    }

    // Password validation
    if ($password === "") {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    // Confirm password validation
    if ($confirmPassword === "") {
        $errors[] = "Please confirm your password.";
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // Check duplicate email
    if (empty($errors)) {

        try {

            if ($user->emailExists($email)) {

                $errors[] = "This email is already registered.";

            } else {

                // Register user
                if ($user->register(
                    $name,
                    $email,
                    $password
                )) {

                    $success = "Registration successful! You can now login.";

                    $name = "";
                    $email = "";

                } else {

                    $errors[] = "Registration failed. Please try again.";
                }
            }

        } catch (PDOException $e) {

            error_log($e->getMessage());

            $errors[] = "Something went wrong. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Signup - Personal Notes</title>

    <link rel="stylesheet"
          href="assets/style.css">

</head>

<body>

<div class="signup-container">

    <div class="signup-box">

        <h1>Create Account</h1>

        <p class="subtitle">
            Join Personal Notes Management
        </p>

        <?php if (!empty($errors)): ?>

            <div class="error-message">

                <?php foreach ($errors as $error): ?>

                    <p>
                        <?= htmlspecialchars($error) ?>
                    </p>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>


        <?php if ($success !== ""): ?>

            <div class="success-message">

                <?= htmlspecialchars($success) ?>

                <p>
                    <a href="login.php">Login Now</a>
                </p>

            </div>

        <?php endif; ?>


        <form method="POST" action="signup.php">

            <div class="form-group">

                <label>Full Name</label>

                <input
                    type="text"
                    name="name"
                    placeholder="Enter your name"
                    value="<?= htmlspecialchars($name) ?>"
                    maxlength="100"
                    required
                >

            </div>


            <div class="form-group">

                <label>Email Address</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?= htmlspecialchars($email) ?>"
                    maxlength="150"
                    required
                >

            </div>


            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter password"
                    minlength="8"
                    required
                >

            </div>


            <div class="form-group">

                <label>Confirm Password</label>

                <input
                    type="password"
                    name="confirm_password"
                    placeholder="Confirm password"
                    required
                >

            </div>


            <button type="submit" class="signup-btn">

                Create Account

            </button>

        </form>


        <p class="login-link">

            Already have an account?

            <a href="login.php">Login</a>

        </p>

    </div>

</div>

</body>
</html>
