<?php 
date_default_timezone_set("Asia/Yangon");

    include "./vendor/autoload.php";
    include "./_actions/main_methods.php";
    include "./_actions/handle_register.php";

    if($error_status) {
        $old = $_POST;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./scss/main.css" />
    <link rel="stylesheet" href="./scss/base.css" />
    <link rel="stylesheet" href="./node_modules/cropperjs/dist/cropper.min.css" />
</head>
<body>
    <div class="alert-box" id="alert-box">
        <div class="preview_area bg-white p-5 rounded shadow">
            <img src="" class="croppingImage shadow d-block mb-3" id="croppingImage" alt="">
            <div class="d-flex align-items-center justify-content-end gap-2 mt-3">
            <button class="btn btn-sm btn-primary ms-auto" id="cropSaveBtn">Crop</button>
            <button class="btn btn-sm btn-danger" id="closeBtn">Close</button>
            </div>
        </div>
    </div>
    <div class="w-100 vh-100 d-flex align-items-center justify-content-center bg-light">
        <div class="col-4">
            <form action="" method="post" enctype="multipart/form-data" class="w-100 rounded p-4 shadow-sm">
                <img src="" id="preview_img" alt="">
                <h1 class="fs-3 fw-bold text-primary mb-2">Readees</h1>
                <small class="text-black-50">Create new account.</small>
                <div class="profileInputInstead shadow" id="profileInputInstead" style="background-image: url(<?= ($error_status) ? $_POST['croppedProfile'] : '' ?>)">
                    <p>+</p>
                </div>
                <input type="file" id="profileInput" class="d-none" accept="image/png, image/jpeg">
                <input type="text" name="croppedProfile" class="d-none" id="croppedProfile" value="<?= ($error_status) ? $_POST['croppedProfile'] : '' ?>">
                <input type="text" class="form-control form-control-sm d-block" name="username" placeholder="Username" value="<?= ($error_status) ? $old['username'] : ''; ?>">
                <small class="text-danger mb-2 d-block"><?= (isset($errors['username'])) ? $errors['username'] : ''; ?></small>
                <input type="text" class="form-control form-control-sm d-block" name="email" placeholder="Email" value="<?= ($error_status) ? $old['email'] : ''; ?>">
                <small class="text-danger mb-2 d-block"><?= (isset($errors['email'])) ? $errors['email'] : ''; ?></small>
                <input type="text" class="form-control form-control-sm d-block" name="password" placeholder="Password">
                <small class="text-danger mb-2  d-block"><?= (isset($errors['password'])) ? $errors['password'] : ''; ?></small>
                <input type="text" class="form-control form-control-sm d-block" name="password_confirmation" placeholder="Confirm password">
                <small class="text-danger mb-2 d-block"><?= (isset($errors['password_confirmation'])) ? $errors['password_confirmation'] : ''; ?></small>
                
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <a href="./login.php" class="small text-decoration-none">Already have an account?</a>
                    <button type="submit" class="btn btn-primary btn-sm">Create</button>
                </div>
            </form>
        </div>
    </div>

    <script src="./node_modules/cropperjs/dist/cropper.min.js"></script>
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
        }
    </script>
</body>
</html>