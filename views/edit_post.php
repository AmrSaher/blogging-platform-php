<?php
    include "../includes/header.php";
    include "../Database.php";

    if (!isAuthed()) {
        header("Location: /views/login.php");
        exit;
    }

    // Get post data
    if (isset($_GET["id"])) {
        $db = new Database();
        $id = $_GET["id"];

        $db->query("SELECT * FROM posts WHERE id = ?;");
        $post = $db->fetchOne([$id]);

        if ($post["user_id"] !== getUser()["id"]) {
            header("Location: /views/");
            exit;
        }
    }

    $errors = [];

    if (isset($_POST["edit_post"])) {
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
            if (empty($data["img"]["name"])) {
                $filePath = $post["image_path"];
            } else {
                $filePath = "../storage/posts/" . time() . $data["img"]["name"];
                move_uploaded_file($data["img"]["tmp_name"], $filePath);
            }

            // Update post data
            $db->query("UPDATE posts SET title = ?, `description` = ?, image_path = ? WHERE id = ?;");
            $db->execute([
                $data["title"],
                $data["desc"],
                str_replace("..", "", $filePath),
                $post["id"]
            ]);

            header("Location: post.php?id={$post['id']}");
            exit;
        }
    }
?>
    <link rel="stylesheet" href="../assets/styles/form.css" />
    <div class="wrapper">
        <form method="post" class="form" enctype="multipart/form-data">
            <h2 class="form-title">Edit Post</h2>
            <div class="field">
                <label for="img">Image</label>
                <input type="file" id="img" name="img" class="inp" placeholder="Enter post image" value="<?= $post["image_path"] ?>">
                <?= isset($errors["image"]) ? '<span class="form-error">' . $errors["image"] . '</span>' : ''; ?>
            </div>
            <div class="field">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" class="inp" placeholder="Enter post title" required autofocus value="<?= $post["title"] ?>">
                <?= isset($errors["title"]) ? '<span class="form-error">' . $errors["title"] . '</span>' : ''; ?>
            </div>
            <div class="field">
                <label for="desc">Description</label>
                <textarea name="desc" id="desc" class="inp" required placeholder="Enter post description"><?= $post["description"] ?></textarea>
                <?= isset($errors["desc"]) ? '<span class="form-error">' . $errors["desc"] . '</span>' : ''; ?>
            </div>
            <input type="submit" value="Edit" class="btn" name="edit_post">
        </form>
    </div>
<?php include "../includes/footer.php"; ?>