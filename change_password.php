<?php
date_default_timezone_set("Asia/Yangon");

session_start();
include "./vendor/autoload.php";
include "./_actions/handle_change_password.php";

use Carbon\Carbon;
use Lib\Helpers\Auth;

    $auth = new Auth();
    $auth->is_authenticated();
    
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
                        <li><a class="dropdown-item" href="./profile.php?u=<?= $_SESSION['auth']['unique_id'] ?>">Profile</a></li>
                        <li><a class="dropdown-item" href="./favorites.php">Saved blogs</a></li>
                        <li><a class="dropdown-item active" href="./change_password.php">Change password</a></li>
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

        <div class="container">
            <div class=" py-3">
                <div class="col-12 col-md-8 col-lg-6 mx-auto px-3 px-lg-5 py-5 my-3 rounded shadow d-block">
                  <form class="w-100" action="" method="POST">
                      <h1 class="fs-3 text-primary mb-4">Password Change</h1>

                      <div class="my-2">
                        <label for="old_password" class="text-black-50 small mb-2">Old password</label>
                        <input type="text" name="old_password" id="#old_password" class="form-control form-control-sm">
                        <small class="text-danger mb-2 d-block"><?= (isset($errors['old_password'])) ? $errors['old_password'] : ''; ?></small>
                      </div>

                      <div class="my-2">
                        <label for="new_password" class="text-black-50 small mb-2">New password</label>
                        <input type="text" name="new_password" id="#new_password" class="form-control form-control-sm">
                        <small class="text-danger mb-2 d-block"><?= (isset($errors['new_password'])) ? $errors['new_password'] : ''; ?></small>
                      </div>

                      <div class="my-2">
                        <label for="confirm_password" class="text-black-50 small mb-2">Confirm password</label>
                        <input type="text" name="confirm_password" id="#confirm_password" class="form-control form-control-sm">
                        <small class="text-danger mb-2 d-block"><?= (isset($errors['confirm_password'])) ? $errors['confirm_password'] : ''; ?></small>
                      </div>
                      <button class="btn btn-primary btn-sm mt-2">Save</button>
                  </form>
                </div>
            </div>
        </div>

    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>