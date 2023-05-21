<?php
    include "../includes/header.php";
    include "../Database.php";

    if (!isAuthed()) {
        header("Location: /views/login.php");
        exit;
    }

    $errors = [];

    if (isset($_POST["add_post"])) {
        $db = new Database();

        $data = [
            "title" => $_POST["title"],
            "desc" => $_POST["desc"],
            "img" => $_FILES["img"]
        ];

        // Validation
        foreach ($data as $key => $value) {
            if (empty($value)) {
                $errors[$key] = ucfirst($key) . " is required.";
            }
        }

        if (count($errors) == 0) {
            // Upload image to storage folder
            $filePath = "../storage/posts/" . time() . $data["img"]["name"];
            move_uploaded_file($data["img"]["tmp_name"], $filePath);

            // Store post data
            $db->query("INSERT INTO posts (title, `description`, image_path, `user_id`) VALUES (?, ?, ?, ?);");
            $db->execute([
                $data["title"],
                $data["desc"],
                str_replace("..", "", $filePath),
                getUser()["id"]
            ]);

            $id = $db->dbh->lastInsertId();
            header("Location: post.php?id=$id");
            exit;
        }
    }
?>
    <link rel="stylesheet" href="../assets/styles/form.css" />
    <div class="wrapper">
        <form method="post" class="form" enctype="multipart/form-data">
            <h2 class="form-title">Add Post</h2>
            <div class="field">
                <label for="img">Image</label>
                <input type="file" id="img" name="img" class="inp" placeholder="Enter post image" required>
                <?= isset($errors["image"]) ? '<span class="form-error">' . $errors["image"] . '</span>' : ''; ?>
            </div>
            <div class="field">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" class="inp" placeholder="Enter post title" required autofocus>
                <?= isset($errors["title"]) ? '<span class="form-error">' . $errors["title"] . '</span>' : ''; ?>
            </div>
            <div class="field">
                <label for="desc">Description</label>
                <textarea name="desc" id="desc" class="inp" required placeholder="Enter post description"></textarea>
                <?= isset($errors["desc"]) ? '<span class="form-error">' . $errors["desc"] . '</span>' : ''; ?>
            </div>
            <input type="submit" value="Post" class="btn" name="add_post">
        </form>
    </div>
<?php include "../includes/footer.php"; ?>