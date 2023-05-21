<?php
    include "../includes/header.php";
    include "../Database.php";

    if (!isAuthed()) {
        header("Location: /views/login.php");
        exit;
    }

    if (isset($_GET["id"])) {
        $db = new Database();
        $id = $_GET["id"];

        $db->query("SELECT * from posts WHERE id = ?;");
        $post = $db->fetchOne([$id]);
    }
?>
    <link rel="stylesheet" href="../assets/styles/post.css" />
    <div class="wrapper">
        <div class="card">
            <img src="<?= $post["image_path"] ?>" alt="post image" class="img">
            <div class="post-details">
                <h2 class="title"><?= $post["title"] ?></h2>
                <p class="desc"><?= $post["description"] ?></p>
                <div>
                    <?php if ($post["user_id"] == getUser()["id"]): ?>
                        <a href="edit_post.php?id=<?= $post["id"] ?>" class="btn">Edit post</a>
                    <?php endif; ?>
                    <span class="date"><?= $post["created_at"] ?></span>
                </div>
            </div>
        </div>
    </div>
<?php include "../includes/footer.php"; ?>