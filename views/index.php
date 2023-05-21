<?php
    include "../includes/header.php";
    include "../Database.php";

    if (!isAuthed()) {
        header("Location: /views/login.php");
        exit;
    }

    $db = new Database();

    $db->query("SELECT * FROM posts;");
    $posts = $db->fetchAll();
?>
    <link rel="stylesheet" href="../assets/styles/home.css" />
    <div class="wrapper">
        <div class="posts">
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="card">
                        <img src="<?= $post["image_path"] ?>" alt="post image" class="card-img">
                        <div class="card-content">
                            <h3 class="card-title"><?= $post["title"] ?></h3>
                            <p class="card-desc"><?= $post["description"] ?></p>
                            <div>
                                <a href="post.php?id=<?= $post["id"] ?>" class="btn">Read more</a>
                                <span class="date"><?= $post["created_at"] ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="margin: auto;">No posts</p>
            <?php endif; ?>
        </div>
    </div>
<?php include "../includes/footer.php"; ?>