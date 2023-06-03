<?php
date_default_timezone_set("Asia/Yangon");

session_start();
include "./vendor/autoload.php";

use Carbon\Carbon;
use Lib\Helpers\Auth;
use Lib\Helpers\HTTP;
use Lib\Database\MySQL;
use Lib\Tables\FavoritesTable;

    $auth = new Auth();
    $auth->is_authenticated();
    $user_id = $_SESSION['auth']['id'];

    $favoritesTable = new FavoritesTable(new MySQL());
    $favorites = $favoritesTable->getFavoritesBlogs($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Readee</title> 
    <link rel="stylesheet" href="./scss/main.css" />
    <link rel="stylesheet" href="./scss/base.css" />
    <link rel="stylesheet" href="./node_modules/@fortawesome/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="./node_modules/cropperjs/dist/cropper.min.css" />
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-primary navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="./">Readees</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <?php
                if(isset($_SESSION['auth'])) : 
            ?>
                <ul class="d-flex align-items-center gap-2 navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="./">Feeds</a>
                    </li>
                    <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= $_SESSION['auth']['username'] ?>
                        <img class="profile bg-white" src="./images/profiles/<?= $_SESSION['auth']['profile'] ?>" alt="">
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="./create_blog.php">New post +</a></li>
                        <li><a class="dropdown-item" href="./profile.php?u=<?= $_SESSION['auth']['unique_id'] ?>">Profile</a></li>
                        <li><a class="dropdown-item active" href="./favorites.php">Saved blogs</a></li>
                        <li><a class="dropdown-item" href="./change_password.php">Change password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout" onclick="event.preventDefault(); logout()">Log out</a></li>
                    </ul>
                    </li>
                </ul>
            <?php else : ?>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="./">Feeds</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="./login.php">Log in</a>
                    </li>
                </ul>
            <?php endif ?>
            </div>
        </div>
    </nav>
    <?php if(count($favorites)): ?>
        <div class="container">
            <div class="row py-4">
                <div class="col-12 col-md-8 col-lg-6 mx-auto">
                    <h1 class="fs-3 mb-4 text-primary">Saved blogs <span class="badge bg-primary"><?= count($favorites) ?></span></h1>
                    <?php foreach($favorites as $favorite): ?>
                        <a href="./blog_detail.php?b=<?= $favorite->unique_id ?>" class="w-100 py-3 px-2 px-md-4 shadow-sm border my-2 d-block text-decoration-none">
                            <h1 class="fs-6 mb-0 text-dark mb-2"><?= $favorite->title ?></h1>
                            <p class="text-truncate small text-black-50 mb-0"><?= $favorite->content ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center mb-0 text-black-50 small mt-3">No Saved blogs yet.</p>
    <?php endif; ?>
    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/main.js"></script>
 
</body>
</html>