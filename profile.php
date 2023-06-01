<?php
date_default_timezone_set("Asia/Yangon");

session_start();
include "./vendor/autoload.php";

use Carbon\Carbon;
use Lib\Helpers\Auth;
use Lib\Helpers\HTTP;
use Lib\Database\MySQL;
use Lib\Tables\UsersTable;

    $auth = new Auth();
    $auth->is_authenticated();

    if(empty($_GET['u'])) {
        HTTP::redirect('./index.php');
    }

    $usersTable = new UsersTable(new MySQL());
    $user = $usersTable->getUser($_GET['u']);

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
                        <img class="profile" src="./images/profiles/<?= $_SESSION['auth']['profile'] ?>" alt="">
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="./create_blog.php">New post +</a></li>
                        <li><a class="dropdown-item active" href="./profile.php?u=<?= $_SESSION['auth']['unique_id'] ?>">Profile</a></li>
                        <li><a class="dropdown-item" href="./favorites.php">Saved blogs</a></li>
                        <li><a class="dropdown-item" href="./change_password.php">Change password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Log out</a></li>
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

    <?php if(isset($user->id)): ?>
        <div class="container">
            <div class=" py-3">
                <div class="col-12 col-md-8 col-lg-6 mx-auto px-3 px-lg-5 py-5 my-3 rounded shadow d-block">
                    <img class="profileImage mb-5" src="./images/profiles/<?= $user->profile ?>" alt="">
                    <h1 class="text-center fs-4 text-dark mb-2"><?= $user->username ?></h1>
                    <p class="mb-4 text-center small text-black-50"><?= $user->email ?></p>
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <div>
                            <div class="personalStatus" title="Blogs">
                                <i class="fa-solid fa-newspaper fa-fw "></i>
                            </div>
                            <p class="text-center mb-0 mt-1"><?= $user->blog_count ?></p>
                        </div>
                        <div>
                            <div class="personalStatus" title="Appreciates">
                                <i class="fa-solid fa-heart fa-fw"></i>
                            </div>
                            <p class="text-center mb-0 mt-1"><?= $user->appreciate_count ?></p>
                        </div>
                    </div>
                    <?php if($user->id == $_SESSION['auth']['id']): ?>
                        <div class="d-flex align-items-center justify-content-center mt-2">
                        <a href="./edit_profile.php" class="btn btn-sm btn-warning mx-auto text-white">Edit</a>  
                        </div>              
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center mb-0 text-black-50 small mt-3">No such user is found.</p>
    <?php endif; ?>
    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
 
</body>
</html>