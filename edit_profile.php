<?php
date_default_timezone_set("Asia/Yangon");

session_start();
include "./vendor/autoload.php";
include "./_actions/main_methods.php";
include "./_actions/handle_change_profile.php";

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
    <div class="alert-box" id="alert-box">
        <div class="preview_area bg-white p-5 rounded shadow">
            <img src="" class="croppingImage shadow d-block mb-3" id="croppingImage" alt="">
            <div class="d-flex align-items-center justify-content-end gap-2 mt-3">
            <button class="btn btn-sm btn-primary ms-auto" id="cropSaveBtn">Crop</button>
            <button class="btn btn-sm btn-danger" id="closeBtn">Close</button>
            </div>
        </div>
    </div>


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
                        <li><a class="dropdown-item" href="./favorites.php">Saved blogs</a></li>
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

        <div class="container">
            <div class=" py-3">
                <div class="col-12 col-md-8 col-lg-6 mx-auto px-3 px-lg-5 py-5 my-3 rounded shadow d-block">
                  <form class="w-100" action="" method="POST">
                      <h1 class="fs-3 text-primary mb-4">Edit profile</h1>

                        <img src="" id="preview_img" alt="">
                        <div class="profileInputInstead shadow" id="profileInputInstead" style="background-image: url(<?= ($error_status) ? $_POST['croppedProfile'] : (($_SESSION['auth']['profile'] != 'default.jpg') ? './images/profiles/'. $_SESSION['auth']['profile'] : '') ?>)">
                            <p>+</p>
                        </div>
                        <input type="file" id="profileInput" class="d-none" accept="image/png, image/jpeg">
                        <input type="text" name="croppedProfile" class="d-none" id="croppedProfile" value="<?= ($error_status) ? $_POST['croppedProfile'] : '' ?>">
                        <div class="d-flex align-items-center justify-content-center">
                            <button id="removeProfileBtn" class="btn btn-sm btn-danger fs-8 <?= ($_SESSION['auth']['profile'] == 'default.jpg') ? 'disabled' : '' ?>" onclick="event.preventDefault();removeProfile(<?= $_SESSION['auth']['id'] ?>);">remove profile</button>
                        </div>

                      <div class="my-2">
                        <label for="username" class="text-black-50 small mb-2">Name</label>
                        <input type="text" name="username" id="#username" class="form-control form-control-sm" value="<?= ($error_status) ? $_POST['username']: $_SESSION['auth']['username'] ?>">
                        <small class="text-danger mb-2 d-block"><?= (isset($errors['username'])) ? $errors['username'] : ''; ?></small>
                      </div>

                      <div class="my-2">
                        <label for="email" class="text-black-50 small mb-2">Email</label>
                        <input type="text" name="email" id="#email" class="form-control form-control-sm" value="<?= ($error_status) ? $_POST['email']: $_SESSION['auth']['email'] ?>">
                        <small class="text-danger mb-2 d-block"><?= (isset($errors['email'])) ? $errors['email'] : ''; ?></small>
                      </div>

                      <button class="btn btn-primary btn-sm mt-2">Save</button>
                  </form>
                </div>
            </div>
        </div>

    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./node_modules/cropperjs/dist/cropper.min.js"></script>
    <script src="./js/main.js"></script>

    <script>
        const profileInput = document.querySelector('#profileInput');
        const previewCropContainer = document.querySelector('#alert-box');
        const previewCropImg = document.getElementById('croppingImage');
        const closeBtn = document.querySelector('#closeBtn')
        const cropSaveBtn = document.querySelector('#cropSaveBtn');
        const profileInputInstead = document.querySelector('#profileInputInstead');
        const croppedProfileInput = document.querySelector('#croppedProfile');


        var cropper = "";

        profileInputInstead.onclick = () => {
            profileInput.click();
        }

        cropSaveBtn.addEventListener('click', (e) => {
            var croppedImage = cropper.getCroppedCanvas().toDataURL("image/png");
            profileInputInstead.style.backgroundImage = `url(${croppedImage})`;
            croppedProfileInput.value = croppedImage;
            console.log(croppedImage);
            previewCropContainer.classList.remove('show');
            cropper.destroy();
            cropper = null;
        });

        function readURL (input) {
            if(input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    previewCropImg.setAttribute('src', e.target.result);
                    cropper = new Cropper(previewCropImg, {
                        aspectRatio: 1,
                        viewMode: 1
                    });
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        profileInput.onchange = (e) => {
            if(e.target.value) {
                readURL(e.target);
                previewCropContainer.classList.add('show');
            } else {
                profileInputInstead.style.backgroundImage = ``;
                croppedProfileInput.value = '';
            }
        }


        closeBtn.onclick = () => {
            cropper.destroy();
            cropper = null;
            croppedProfileInput.value = '';
            previewCropContainer.classList.remove('show');
            profileInputInstead.style.backgroundImage = ``;
            profileInput.value = '';
        }

        function removeProfile(userId) {
            if(confirm("Are you sure to remove profile picture?")) {
                userData = new FormData();
                userData.append('userId', userId);

                fetch('./_actions/handle_remove_profile.php', {
                    method: 'POST',
                    body: userData
                }).then(res => res.json())
                .then(data => {
                    if(data.status == 500) {
                        console.log(data.message);
                    }

                    if(data.status == 200) {
                        document.querySelector('#profileInputInstead').style.backgroundImage='';
                        document.querySelector('#removeProfileBtn').classList.add('disabled');
                    }
                })
            }
        }
    </script>
</body>
</html>