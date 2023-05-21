<?php
    include "../includes/header.php";
    include "../Database.php";

    if (isAuthed()) {
        header("Location: /views/");
        exit;
    }

    $errors = [];

    if (isset($_POST["register"])) {
        $db = new Database();

        $data = [
            "name" => $_POST["name"],
            "email" => $_POST["email"],
            "password" => $_POST["password"],
            "hash_password" => md5($_POST["password"]),
            "password_confirmation" => $_POST["password_confirmation"]
        ];

        // Validation
        foreach ($data as $key => $value) {
            if (empty($value)) {
                $errors[$key] = ucfirst($key) . " is required.";
            }
        }
        if ($data["password"] !== $data["password_confirmation"]) {
            $errors["password"] = "The entered passwords doesnt match.";
        }
        if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Please enter a valid email.";
        }

        // Store user data
        if (count($errors) == 0) {
            $db->query("INSERT INTO users (`name`, email, `password`) VALUES (?, ?, ?);");
            $db->execute([
                $data["name"],
                $data["email"],
                $data["hash_password"]
            ]);

            $db->query("SELECT * FROM users WHERE email = ?;");
            $_SESSION["user"] = $db->fetchOne([$data["email"]]);
            header("Location: /views/");
            exit;
        }
    }
?>
    <link rel="stylesheet" href="../assets/styles/form.css" />
    <div class="wrapper">
        <form method="post" class="form">
            <h2 class="form-title">Register</h2>
            <div class="field">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="inp" placeholder="Enter your name" autofocus required>
                <?= isset($errors["name"]) ? '<span class="form-error">' . $errors["name"] . '</span>' : ''; ?>
            </div>
            <div class="field">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="inp" placeholder="Enter your email address" required>
                <?= isset($errors["email"]) ? '<span class="form-error">' . $errors["email"] . '</span>' : ''; ?>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="inp" placeholder="Enter your password" required>
                <?= isset($errors["password"]) ? '<span class="form-error">' . $errors["password"] . '</span>' : ''; ?>
            </div>
            <div class="field">
                <label for="password_confirmation">Re-type Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="inp" placeholder="Confirm your name" required>
            </div>
            <input type="submit" value="Register" class="btn" name="register">
        </form>
    </div>
<?php include "../includes/footer.php"; ?>