<?php 
date_default_timezone_set("Asia/Yangon");

use Lib\Helpers\HTTP;
use Lib\Database\MySQL;
use Lib\Tables\BlogsTable;
    session_start();
    include "./vendor/autoload.php";
    include "./_actions/handle_edit_blog.php";

    use Lib\Helpers\Auth;

    $auth = new Auth();
    $auth->is_authenticated();

    if(empty($_GET['b'])) {
        HTTP::redirect('index.php');
    } else {
        $blogsTable = new BlogsTable(new MySQL());
        $blog = $blogsTable->getBlog($_GET['b']);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Readees</title>
    <link rel="stylesheet" href="./scss/main.css" />
    <link rel="stylesheet" href="./scss/base.css" />
    <link rel="stylesheet" href="./node_modules/cropperjs/dist/cropper.min.css" />
    <link rel="stylesheet" href="./node_modules/cropperjs/dist/cropper.min.css" />
    <link rel="stylesheet" href="./node_modules/@fortawesome/fontawesome-free/css/all.min.css" />
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
        
    <?php if(isset($blog->id)): ?>
        <div class="container py-2 mt-5">
            <form action="" class="col-12 col-md-8 col-lg-6 p-5 rounded shadow bg-light mx-auto" method="POST" enctype="multipart/form-data">
                <h1 class="text-primary fw-bold fs-3 mb-4">Modify blog</h1>
                <input type="text" value="<?= $_GET['b'] ?>" name="id" class="d-none">
                <!-- <small class="text-black-50 mb-4 d-block">Create your new blog and be popular in Readees!!!</small> -->
                
                <div class="my-2 position-relative cover_cont">
                    <label for="coverInput" class="text-black-50 fs-7">Select your blogs cover</label>
                    <div class="coverInsteadInput shadow" id="coverInsteadInput" style="background-image: url(<?= ($error_status) ? $_POST['cover'] : './images/covers/' . $blog->cover ?>)">
                        +
                    </div>
                    <button class="btn btn-sm btn-danger mx-auto d-block fs-8 <?= (empty($blog->cover)) ? 'disabled' : '' ?>" onclick="removeCover(event, '<?= $_GET['b'] ?>')">remove current cover</button>
                    <input type="file" class="d-none" accept="image/png, image/jpeg" id="coverInput">
                    <input type="text" class="d-none" name="cover" value="<?= ($error_status) ? $_POST['cover'] : '' ?>" id="coverTextInput">
                </div>
                <div class="my-2">
                    <label for="title" class="text-black-50 d-block mb-1 fs-7">Title</label>
                    <input type="text" class="form-control form-control-sm" name="title" value="<?= ($error_status) ? $_POST['title'] : $blog->title ?>">
                    <small class="text-danger ps-1 d-block"><?= (isset($errors['title'])) ?$errors['title'] : '' ?></small>
                </div>

                
                <label for="content" class="text-black-50 d-block mb-1 fs-7">Body</label>
                <textarea name="content" id="content" rows="14" class="form-control form-control-sm"><?= ($error_status) ? $_POST['content'] : $blog->content ?></textarea>
                <small class="text-danger ps-1 d-block mb-3"><?= (isset($errors['content'])) ?$errors['content'] : '' ?></small>
                <div class="mt-2 d-flex align-items-center justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm ">Save</button>
                </div>
            </form>
        </div>
        <?php else: ?>
            <p class="text-center mb-0 text-black-50 small mt-3">No such blog is found.</p>
        <?php endif; ?>

    <script src="./node_modules/cropperjs/dist/cropper.min.js"></script>
    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="./js/main.js"></script>
    <script>

        const coverInsteadInput = document.querySelector('#coverInsteadInput');
        const coverInput = document.querySelector('#coverInput');
        const cropContainer = document.querySelector('#alert-box');
        const croppingImage = document.querySelector('#croppingImage');
        const coverTextInput = document.querySelector('#coverTextInput');

        const saveBtn = document.querySelector('#cropSaveBtn');
        const closeBtn = document.querySelector('#closeBtn');

        var cropper;

        function readURL (input) {
            if(input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload  = (e) => {
                    croppingImage.setAttribute('src', e.target.result);
                    cropper = new Cropper(croppingImage, {
                        aspectRatio: 3/4,
                        viewMode: 1
                    });
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        saveBtn.addEventListener ('click', (e) => {
            var croppedImage = cropper.getCroppedCanvas().toDataURL("image/png");
            coverInsteadInput.style.backgroundImage = `url(${croppedImage})`;
            coverTextInput.value = croppedImage;
            cropContainer.classList.remove('show');
            cropper.destroy();
            cropper = null;
        })

        closeBtn.addEventListener('click', (e) => {
            cropContainer.classList.remove('show');
            cropper.destroy();
            cropper = null;
            coverInsteadInput.style.backgroundImage = '';
            coverTextInput.value = '';
            coverInput.value = '';
        })

        coverInsteadInput.addEventListener('click', (e) => {
            coverInput.click();
        })

        coverInput.addEventListener ('change', (e) => {
            if(e.target.value) {
                readURL(e.target);
                cropContainer.classList.add('show');
            } else {
                coverInsteadInput.style.backgroundImage = ``;
                coverTextInput.value = '';
                coverInput.value = '';
            }
        })


        function removeCover (event,id) {
            event.preventDefault();

            if(confirm("Are you sure to remove?")) {
                fetch("./_actions/handle_remove_cover.php?id=" + id).then(res => res.json())
                .then(data => {
                    if(data.status === 200) {
                        coverInsteadInput.style.backgroundImage = ``;
                        coverTextInput.value = '';
                        event.target.classList.add('disabled');
                    } 
                    if(data.status === 400) {
                        console.log(data.message);
                    }

                    if(data.status === 404) {
                        console.log(data.message);
                    }
                })
            }
        }

    </script>
</body>
</html>