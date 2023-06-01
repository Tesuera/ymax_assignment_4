<?php
date_default_timezone_set("Asia/Yangon");

session_start();
include "./vendor/autoload.php";

use Carbon\Carbon;
use Lib\Helpers\Auth;
use Lib\Database\MySQL;
use Lib\Tables\BlogsTable;
use Lib\Tables\ViewsTable;
use Lib\Tables\CommentsTable;
use Lib\Tables\FavoritesTable;
use Lib\Tables\AppreciateTable;

    $auth = new Auth();
    $auth->is_authenticated();
    
    $blogsTable = new BlogsTable(new MySQL());
    $favoritesTable = new FavoritesTable(new MySQL());
    $appreciatesTable = new AppreciateTable(new MySQL());
    $viewsTable = new ViewsTable(new MySQL());
    $commentsTable = new CommentsTable(new MySQL());

    $blogs = $blogsTable->getAllBlogs();
    $favoritesByLoginUser = $favoritesTable->getAllFavoritesByLoginUser();
    $appreciatesByLoginUser = $appreciatesTable->getAllAppreciatesByLoginUser();

    $favorites = array_column($favoritesByLoginUser, 'blog_id');
    $appreciates = array_column($appreciatesByLoginUser, 'blog_id');

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
    <link rel="stylesheet" href="./node_modules/@fortawesome/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="./node_modules/cropperjs/dist/cropper.min.css" />
</head>
<body class="bg-light">

    <!-- Modal -->
    <div class="modal fade" id="showAppreciatesModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="appreciated_blog_title"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="appreciated_blog_body">
            
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div> -->
            </div>
        </div>
    </div>
    <div class="alertInfo">
        <p class="mb-0 text-light fs-7" id="alertInfoContent"></p>
    </div>

    <div class="biggerImage" id="biggerImage">
        <div class="backgroundBigger"></div>
        <img src="" class="imageDetail" id="imageDetail" alt="">
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
                    <a class="nav-link active" aria-current="page" href="./">Feeds</a>
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

    <div class="container">
        <div class=" py-3">
            <?php if(count($blogs)) : ?>
                <?php foreach($blogs as $blog) : ?>
                    <div id="blogCont<?= $blog->id ?>">
                        <div class="col-12 col-md-8 col-lg-6 mx-auto px-3 px-lg-5 py-5 my-3 rounded shadow d-block" id="blog<?= $blog->id ?>">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="w-50 d-flex align-items-start gap-3">
                                    <div class="position-relative profileCont" onclick="goToUserDetail('<?= $blog->userUniqueId ?>')" id="profileCont<?=$blog->id ?>">
                                        <img src="./images/profiles/<?= $blog->userProfile ?>" alt="<?= $blog->userName ?>" class="blogProfile">
                                        <div class="eachProfileDetail d-flex align-items-start gap-3 shadow bg-white  py-3 px-4">
                                            <img src="./images/profiles/<?= $blog->userProfile?>" class="profileDatailImage" alt="">

                                            <div class="detailContentCont pt-2">
                                                <h1 class="text-dark fw-bold fs-5 mb-1 text-break"><?= $blog->userName ?></h1>
                                                <div class="d-flex align-items-start gap-2 w-100 mb-3">
                                                    <i class="fa-regular fa-envelope text-black-50 pt-1 fs-7"></i>
                                                    <p class="text-black-50 w-100 mb-0 text-break fs-7"><?= $blog->userEmail ?></p>
                                                </div>
                                                <a href="./profile.php?u=<?= $blog->userUniqueId ?>" class="btn btn-primary btn-sm">See profile</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">
                                        <h1 class="blogUsername mb-0"><?= $blog->userName ?></h1>
                                        <small class="d-block blogCreatedTime text-block-50"><?= Carbon::create($blog->created_at)->diffforHumans() ?></small>
                                    </div>
                                </div>
                                <div class="">
                                    <button class="btn btn-sm bg-light"  data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical cursor-pointer" ></i></button>
                                    <ul class="dropdown-menu">
                                        <div id="blog_menu<?= $blog->id ?>">
                                            <?php if(in_array($blog->id, $favorites)): ?>
                                                <li class="small"><button class="dropdown-item" href="" onclick="removeFromFavorites(event, <?= $blog->id ?>)"><i class="fa-solid fa-bookmark me-2 fa-fw"></i> Unsave blog</butt></li>
                                            <?php else: ?>
                                                <li class="small"><button class="dropdown-item" href="" onclick="addToFavorites(event, <?= $blog->id ?>)"><i class="fa-regular fa-bookmark me-2 fa-fw"></i> Save blog</button></li>
                                            <?php endif; ?>
                                        </div>
                                        <li class="small"><a class="dropdown-item" href="./blog_detail.php?b=<?= $blog->unique_id ?>" onclick='copyLink(event, "<?= $blog->unique_id  ?>")'><i class="fa-solid fa-link fa-fw me-2"></i> Copy link</a></li>
                                        <li class="small"><a class="dropdown-item" href="#" onclick="hideBlog(event, <?= $blog->id ?>)"><i class="fa-regular fa-rectangle-xmark me-2 fa-fw"></i> Hide this blog</a></li>
                                        <?php if($blog->user_id == $_SESSION['auth']['id']): ?>
                                            <li class="small"><a class="dropdown-item" href="./edit_blog.php?b=<?= $blog->unique_id ?>"><i class="fa-solid fa-pen-to-square me-2 fa-fw"></i> Edit blog</a></li>
                                            <li class="small"><a class="dropdown-item" href="delete" onclick="deleteBlog(event, <?= $blog->id ?>)"><i class="fa-solid fa-trash me-2 fa-fw"></i> Delete</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <?php if($blog->cover != null): ?>
                                    <div class="p-4">
                                        <img class="blogCover" onclick="showBlogCover(event)" src="./images/covers/<?= $blog->cover ?>" alt="">
                                    </div>
                                <?php endif; ?>
                                <div class="">
                                    <a title="detail" href="./blog_detail.php?b=<?= $blog->unique_id ?>" class="text-decoration-none d-block rounded blogTitle px-4 py-3 mb-2"><p class="text-dark fw-bold mb-0"><?= $blog->title ?></p></a>
                                    <?php if(strlen($blog->content) > 200) : ?>
                                        <small class="text-black-50 px-4 d-block eachBlogContent" id="shortContent<?= $blog->id ?>"><?= substr($blog->content, 0, 200)?> ...<p onclick="(seeMore(<?= $blog->id ?>))" class='text-decoration-underline ms-1 text-primary d-inline cursor-pointer'>see more</p></small>
                                        <small class="text-black-50 px-4 d-none eachBlogContent" id="longContent<?= $blog->id ?>"><?= $blog->content ?></small>
                                    <?php else: ?>
                                        <small class="text-black-50 px-4" ><?= $blog->content ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex w-100 align-items-center px-4 justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center gap-1">
                                        <div id="blogAppreciate<?= $blog->id ?>">
                                            <?php if(in_array($blog->id, $appreciates)): ?>
                                                <i class="fa-solid appreciate text-danger fa-heart fa-fw cursor-pointer" title="undo" onclick="removeAppreciate(event, <?= $blog->id ?>)"></i>
                                            <?php else: ?>
                                                <i class="fa-regular appreciate text-dark fa-heart fa-fw cursor-pointer" title="appreciate" onclick="appreciate(event, <?= $blog->id ?>)"></i>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-dark showAppreciates" onclick="seeWhoAppreciate(event, <?= $blog->id ?>, '<?= $blog->title ?>', <?= $_SESSION['auth']['id'] ?>)" id="appreciatesCount<?= $blog->id ?>" title="see who appreciates"><?= $appreciatesTable->appreciatesCountByBlog($blog->id)->count; ?></small>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 cursor-pointer text-dark commentcont" title="comments" onclick="showComments('<?= $blog->unique_id ?>')">
                                        <i class="fa-regular fa-comment fa-fw"></i>
                                        <small class=""><?= $commentsTable->commentsCountByBlog($blog->id); ?></small>
                                    </div>
                                </div>            
                                <div class="">
                                    <p class="text-black-50 fs-7 mb-0"><?= $viewsTable->getAllViewCountByBlog($blog->id); ?>  <i class="fa-regular fa-eye fa-fw fs-8 text-black-50 ms-1"></i></p>
                                </div>
                            </div>
                        </div>

                        <!-- hidden blog -->
                        <div class="col-12 col-md-8 col-lg-6 mx-auto px-3 px-lg-5 py-3 my-3 rounded shadow align-items-center justify-content-between d-none" id="hiddenBlog<?= $blog->id ?>">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fa-regular fa-rectangle-xmark fs-5 text-black-50"></i>
                                <div class="">
                                    <p class="mb-0 fw-bold text-dark">Blog hidden</p>
                                    <small class="mb-0 d-block fs-7 text-black-50">You won't see this blog in your feed.</small>
                                </div>
                            </div>

                            <button class="btn btn-sm btn-light shadow text-dark" onclick="undoHide(<?= $blog->id ?>)">Undo</button>
                        </div>
                        <!-- hidden blog -->
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="text-center mb-0 text-black-50 small mt-3">No blogs yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        let showAppreciatesModal = new bootstrap.Modal(document.querySelector('#showAppreciatesModal'));

        function goToUserDetail (id) {
            console.log(id);
        }

        function seeMore (id) {
            document.querySelector(`#shortContent${id}`).classList.remove('d-block');
            document.querySelector(`#shortContent${id}`).classList.add('d-none');

            document.querySelector(`#longContent${id}`).classList.remove('d-none');
            document.querySelector(`#longContent${id}`).classList.add('d-block');
        }

        function hideBlog (event, id) {
            event.preventDefault();
            document.querySelector(`#blog${id}`).classList.remove('d-block');
            document.querySelector(`#blog${id}`).classList.add('d-none');

            document.querySelector(`#hiddenBlog${id}`).classList.remove('d-none');
            document.querySelector(`#hiddenBlog${id}`).classList.add('d-flex');
        }

        function undoHide (id) {
            document.querySelector(`#blog${id}`).classList.add('d-block');
            document.querySelector(`#blog${id}`).classList.remove('d-none');

            document.querySelector(`#hiddenBlog${id}`).classList.add('d-none');
            document.querySelector(`#hiddenBlog${id}`).classList.remove('d-flex');
        }

        function copyLink (event, unique_id) {
            event.preventDefault();
            let baseUrl = "http://localhost/ymax_php_assignment/";

            
            let textarea = document.createElement('textarea');


            textarea.innerHTML = baseUrl + 'blog_detail.php?b=' + unique_id;
            document.body.append(textarea);
            textarea.select();
            document.execCommand('copy');

            document.querySelector('#alertInfoContent').textContent = 'Copied';
            document.querySelector('.alertInfo').classList.add('showAlert');

            document.querySelector('.alertInfo').addEventListener('animationend', function (e) {
                document.querySelector('.alertInfo').classList.remove('showAlert');
            })
            textarea.remove();
        }

        function showBlogCover (event) {
            document.querySelector('#imageDetail').setAttribute('src', event.target.getAttribute('src'));
            document.querySelector('#biggerImage').classList.add('show');
       }


       document.querySelector('.backgroundBigger').addEventListener('click', function () {
            document.querySelector('#biggerImage').classList.remove('show');
       })

       function deleteBlog (event, id) {
            event.preventDefault();

            if(id !== undefined || id !== 0 || id !== null) {
               if(confirm("Are you sure to delete?")) {
                fetch("./_actions/handle_delete_blog.php?id=" + id).then(res => res.json())
                .then(data => {
                    if(data.status === 200) {
                        document.querySelector('#blogCont' + id).remove();
                    }

                    if(data.status === 404) {
                        console.log(data.message);
                    }

                    if(data.status === 500) {
                        console.log(data.message);
                    }
                });
               }
            }
        }

        // add favorite remove favorite

        function addToFavorites (event, blogId) {
            event.preventDefault();
            const blogData = new FormData();
            blogData.append("blogId", blogId);

            fetch("./_actions/handle_add_favorites.php", {
                method: 'POST',
                body: blogData
            }).then(res => res.json())
            .then(data => {
                if(data.status == 400) {
                    alert(data.message);
                }

                if(data.status == 200) {
                    event.target.remove();
                    document.querySelector('#blog_menu' + blogId).innerHTML = ` <li class="small"><button class="dropdown-item" href="" onclick="removeFromFavorites(event, ${blogId})"><i class="fa-solid fa-bookmark me-2 fa-fw"></i> Unsave blog</button></li>`

                    document.querySelector('#alertInfoContent').textContent = 'Blog added to favorites';
                    document.querySelector('.alertInfo').classList.add('showAlert');

                    document.querySelector('.alertInfo').addEventListener('animationend', function (e) {
                        document.querySelector('.alertInfo').classList.remove('showAlert');
                    })
                }
            })
        }


        function removeFromFavorites (event, blogId) {
            event.preventDefault();

            blogData = new FormData();
            blogData.append("blogId", blogId);

            fetch("./_actions/handle_remove_favorites.php", {
                method: 'POST',
                body: blogData
            }).then(res => res.json())
            .then(data => {
                if(data.status == 200) {
                    event.target.remove();
                    document.querySelector('#blog_menu' + blogId).innerHTML = `
                    <li class="small"><button class="dropdown-item" href="" onclick="addToFavorites(event, ${blogId})"><i class="fa-regular fa-bookmark me-2 fa-fw"></i> Save blog</button></li>`;

                    document.querySelector('#alertInfoContent').textContent = 'Blog removed from favorites';
                    document.querySelector('.alertInfo').classList.add('showAlert');

                    document.querySelector('.alertInfo').addEventListener('animationend', function (e) {
                        document.querySelector('.alertInfo').classList.remove('showAlert');
                    })
                } 

                if(data.status == 400) {
                    console.log(data.message);
                }
            })
        }
        // favorite end

        // appreciate start
        function appreciate (event, blogId) {
            event.preventDefault();

            if(blogId !== undefined && blogId !== null) {
                blogData = new FormData();
                blogData.append("blogId", blogId);

                fetch("./_actions/handle_appreciate.php", {
                    method: 'POST',
                    body: blogData
                }).then(res => res.json())
                .then(data => {
                    if(data.status == 400) {
                        console.log(data.message);
                    }

                    if(data.status == 200) {
                        event.target.remove();
                        let counts = document.querySelector('#appreciatesCount' + blogId).textContent;

                        document.querySelector('#blogAppreciate' + blogId).innerHTML = `
                        <i class="fa-solid appreciate text-danger fa-heart fa-fw cursor-pointer" title="undo" onclick="removeAppreciate(event, ${blogId})"></i>
                        `;
                        document.querySelector('#appreciatesCount' + blogId).textContent = parseInt(counts) + 1;
                    }
                })
            }
        }

        function removeAppreciate (event, blogId) {
            event.preventDefault();

            if(blogId !== undefined && blogId !== null) {
                blogData = new FormData();
                blogData.append("blogId", blogId);

                fetch('./_actions/handle_remove_appreciate.php', {
                    method: 'POST',
                    body: blogData
                }).then(res => res.json())
                .then(data => {
                    if(data.status == 400) {
                        console.log(data.message);
                    }

                    if(data.status == 200) {
                        event.target.remove();
                        let counts = document.querySelector('#appreciatesCount' + blogId).textContent;
                        
                        document.querySelector('#blogAppreciate' + blogId).innerHTML = `
                        <i class="fa-regular appreciate text-dark fa-heart fa-fw cursor-pointer" title="appreciate" onclick="appreciate(event, ${blogId})"></i>
                        `;

                        document.querySelector('#appreciatesCount' + blogId).textContent = parseInt(counts) - 1;
                    }
                })
            }
        }

        function seeWhoAppreciate (event, blogId, title, currentUserId) {
            event.preventDefault();
            
            console.log(currentUserId);
            if(blogId != null && blogId != undefined) {
                document.querySelector('#appreciated_blog_title').textContent = `People who apprecited ${title}`; 
                document.querySelector('#appreciated_blog_body').innerHTML = `
                <div class="loader_container">
                    <span class="loader"></span>
                </div>
                `           
                showAppreciatesModal.show();

                blogData = new FormData();
                blogData.append('blogId', blogId);
                fetch('./_actions/handle_get_appreciates_by_blog.php', {
                    method: 'POST',
                    body: blogData
                }).then(res => res.json())
                .then(data => {
                    if(data.status == 400) {
                        document.querySelector('#appreciated_blog_body').innerHTML = `
                        <p class="text-center my-3">No appreciates yet</p>
                        ` 
                    }

                    if(data.status == 200) {
                        let listString = '';
                        data.users.forEach(user => {
                            listString += `
                                <div class="py-2 px-4 each_appreciate_cont">
                                    <div class="d-flex gap-3 align-items-center justify-content-center">
                                        <a href=""><img src="./images/profiles/${user.profile}" class="blogProfile" alt="${user.username}" /></a>
                                        <p class="fs-7 text-dark mb-0">${(user.id == currentUserId ) ? 'You' : user.username}</p>
                                    </div>

                                    <i class="fa-solid fa-heart fa-fw text-danger fs-7"></i>
                                </div>                            
                            `;
                        })
                        document.querySelector('#appreciated_blog_body').innerHTML = listString;
                    }
                })
            }
        }
        
        // appreciate end


        // comments start

        function showComments (blogId) {
            if(blogId != null || blogId !== undefined) {
                location.href = "./blog_detail.php?b=" + blogId + "#commentTitle";
            }
        }

        // comments end
    </script>
</body>
</html>