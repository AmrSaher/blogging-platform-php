<?php
    include "../includes/header.php";
    include "../Database.php";

    if (isAuthed()) {
        header("Location: /views/");
        exit;
    }

    $errors = [];

    if (isset($_POST["login"])) {
        $db = new Database();

        $data = [
            "email" => $_POST["email"],
            "password" => $_POST["password"],
            "hash_password" => md5($_POST["password"])
        ];

        // Validation
        foreach ($data as $key => $value) {
            if (empty($value)) {
                $errors[$key] = ucfirst($key) . " is required.";
            }
        }
        if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Please enter a valid email.";
        }

        // Store user data
        if (count($errors) == 0) {
            $db->query("SELECT * FROM users WHERE email = ?;");
            $user = $db->fetchOne([$data["email"]]);

            if (is_bool($user)) {
                $errors["email"] = "Email is not exist.";
            } else {
                if ($user["password"] === $data["hash_password"]) {
                    $_SESSION["user"] = $user;
                    header("Location: /views/");
                    exit;
                } else {
                    $errors["email"] = "Email or password is wrong.";
                }
            }
        }
    }
?>
    <link rel="stylesheet" href="../assets/styles/form.css" />
    <div class="wrapper">
        <form method="post" class="form">
            <h2 class="form-title">Login</h2>
            <div class="field">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="inp" placeholder="Enter your email address" required autofocus>
                <?= isset($errors["email"]) ? '<span class="form-error">' . $errors["email"] . '</span>' : ''; ?>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="inp" placeholder="Enter your password" required>
                <?= isset($errors["password"]) ? '<span class="form-error">' . $errors["password"] . '</span>' : ''; ?>
            </div>
            <input type="submit" value="Login" class="btn" name="login">
        </form>
    </div>
<?php include "../includes/footer.php"; ?>