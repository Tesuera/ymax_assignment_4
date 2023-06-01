<?php 
date_default_timezone_set("Asia/Yangon");
    include "./vendor/autoload.php";
    include "./_actions/main_methods.php";
    include "./_actions/handle_login.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./scss/main.css" />
    <link rel="stylesheet" href="./scss/base.css" />
    <link rel="stylesheet" href="./node_modules/cropperjs/dist/cropper.min.css" />
</head>
<body>
    <div class="w-100 vh-100 d-flex align-items-center justify-content-center">
        <div class="col-4">
            <form action="" method="POST" class="bg-white shadow p-5">
                <h1 class="fw-bold text-primary fs-3 mb-2">Readees</h1>
                <small class="text-black-50 mb-2">Log in to your account. welcome back.</small>

                <input type="text" class="form-control form-control-sm d-block mt-3" name="email" placeholder="Email" value="<?= ($error_status) ? $_POST['email']: '' ?>">
                <small class="text-danger ps-2 mb-2 d-block"><?= (isset($errors['email'])) ? $errors['email'] : '' ?></small>

                <input type="text" class="form-control form-control-sm d-block" placeholder="Password" name="password">
                <small class="text-danger ps-2 mb-2 d-block"><?= (isset($errors['password'])) ? $errors['password'] : '' ?></small>

                <div class="d-flex align-items-center justify-content-between mt-3">
                    <a href="./register.php" class="text-decoration-none small">Don't have an account?</a>
                    <button class="btn btn-sm btn-primary" type="submit">Log in</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>