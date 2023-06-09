<?php

date_default_timezone_set("Asia/Yangon");

session_start();
include "./vendor/autoload.php";

use Carbon\Carbon;
use Lib\Helpers\Auth;
use Lib\Helpers\HTTP;
use Lib\Database\MySQL;
use Lib\Tables\BlogsTable;
use Lib\Tables\ViewsTable;
use Lib\Tables\CommentsTable;
use Lib\Tables\FavoritesTable;
use Lib\Tables\AppreciateTable;

    $auth = new Auth();
    $auth->is_authenticated();
    
    if(!empty($_GET['b'])) {
        $blogsTable = new BlogsTable(new MySQL());
        $favoritesTable = new FavoritesTable(new MySQL());
        $appreciatesTable = new AppreciateTable(new MySQL());
        $viewsTable = new ViewsTable(new MySQL());
        $commentsTable = new CommentsTable(new MySQL());

        $row = $blogsTable->getBlogDetail($_GET['b']);
        if(isset($row->id)) {
            $is_favorite = $favoritesTable->checkingStatus($row->id, $_SESSION['auth']['id']);
            $comments = $commentsTable->getAllCommentsByBlog($row->id);
        }
    } else {
        HTTP::redirect('index.php');
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
    <div class="biggerImage" id="biggerImage">
        <div class="backgroundBigger"></div>
        <img src="" class="imageDetail" id="imageDetail" alt="">
    </div>
    <div class="alertInfo">
        <p class="mb-0 text-light fs-7" id="alertInfoContent"></p>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="alertToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Readees</strong>
                <small>now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastContent">

            </div>
        </div>
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
                        <li><a class="dropdown-item" href="./profile.php">Saved blogs</a></li>
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
            <?php if(isset($row->id)) : ?>
                <div class="col-12 col-md-8 col-lg-6 mx-auto px-3 px-lg-5 pt-5 rounded shadow d-block" id="blog<?= $row->id ?>">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="w-75 d-flex align-items-start gap-3">
                            <div class="position-relative profileCont" onclick="goToUserDetail('<?= $row->userUniqueId ?>')" id="profileCont<?=$row->id ?>">
                                <img src="./images/profiles/<?= $row->userProfile ?>" alt="<?= $row->userName ?>" class="blogProfile">
                                <div class="eachProfileDetail d-flex align-items-start gap-3 shadow bg-white  py-3 px-4">
                                    <img src="./images/profiles/<?= $row->userProfile?>" class="profileDatailImage" alt="">

                                    <div class="detailContentCont pt-2">
                                        <h1 class="text-dark fw-bold fs-5 mb-1 text-break"><?= $row->userName ?></h1>
                                        <div class="d-flex align-items-start gap-2 w-100 mb-3">
                                            <i class="fa-regular fa-envelope text-black-50 pt-1 fs-7"></i>
                                            <p class="text-black-50 w-100 mb-0 text-break fs-7"><?= $row->userEmail ?></p>
                                        </div>
                                        <a href="./profile.php?u=<?= $row->userUniqueId ?>" class="btn btn-primary btn-sm">See profile</a>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <h1 class="blogUsername mb-0"><?= $row->userName ?></h1>
                                <small class="d-block blogCreatedTime text-block-50"><?= Carbon::create($row->created_at)->diffforHumans() ?></small>
                            </div>
                        </div>
                        <div class="">
                            <button class="btn btn-sm bg-light"  data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical cursor-pointer" ></i></button>
                            <ul class="dropdown-menu">
                                <div id="blog_menu">
                                    <?php if($is_favorite): ?>
                                        <li class="small"><button class="dropdown-item" href="" onclick="removeFromFavorites(event, <?= $row->id ?>)"><i class="fa-solid fa-bookmark me-2 fa-fw"></i> Unsave blog</butt></li>
                                    <?php else: ?>
                                        <li class="small"><button class="dropdown-item" href="" onclick="addToFavorites(event, <?= $row->id ?>)"><i class="fa-regular fa-bookmark me-2 fa-fw"></i> Save blog</button></li>
                                    <?php endif; ?>
                                </div>
                                <li class="small"><a class="dropdown-item" href="./blog_detail.php?b=<?= $row->unique_id ?>" onclick='copyLink(event, "<?= $row->unique_id  ?>")'><i class="fa-solid fa-link fa-fw me-2"></i> Copy link</a></li>
                                <?php if($row->user_id == $_SESSION['auth']['id']): ?>
                                    <li class="small"><a class="dropdown-item" href="./edit_blog.php?b=<?= $row->unique_id ?>"><i class="fa-solid fa-pen-to-square me-2 fa-fw"></i> Edit blog</a></li>
                                    <li class="small"><a class="dropdown-item" href="delete" onclick="deleteBlog(event, <?= $row->id ?>)"><i class="fa-solid fa-trash me-2 fa-fw"></i> Delete</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <?php if($row->cover != null): ?>
                            <div class="p-4">
                                <img class="blogCover" onclick="showBlogCover(event)" src="./images/covers/<?= $row->cover ?>" alt="">
                            </div>
                        <?php endif; ?>
                        <div class="">
                            <p class="text-dark fw-bold mb-0 px-2 px-md-4 py-3 mb-2"><?= $row->title ?></p>
                            <small class="text-black-50 px-2 px-md-4 d-block eachBlogContent" ><?= $row->content ?></small>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex w-100 align-items-center px-2 px-md justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-1">
                                <div id="blogAppreciate<?= $row->id ?>">
                                    <?php if($appreciatesTable->checkingStatus($_SESSION['auth']['id'], $row->id)): ?>
                                        <i class="fa-solid appreciate text-danger fa-heart fa-fw cursor-pointer" title="undo" onclick="removeAppreciate(event, <?= $row->id ?>)"></i>
                                    <?php else: ?>
                                        <i class="fa-regular appreciate text-dark fa-heart fa-fw cursor-pointer" title="appreciate" onclick="appreciate(event, <?= $row->id ?>)"></i>
                                    <?php endif; ?>
                                </div>
                                <small class="text-dark showAppreciates" onclick="seeWhoAppreciate(event, <?= $row->id ?>, '<?= $row->title ?>', <?= $_SESSION['auth']['id'] ?>)" id="appreciatesCount<?= $row->id ?>" title="see who appreciates"><?= $appreciatesTable->appreciatesCountByBlog($row->id)->count ?></small>
                            </div>
                            <a href="#commentTitle" class="d-flex text-decoration-none align-items-center gap-1 cursor-pointer text-dark commentcont" title="comments">
                                <i class="fa-regular fa-comment fa-fw"></i>
                                <small class="" id="commentCount"><?= $commentsTable->commentsCountByBlog($row->id) ?></small>
                            </a>
                        </div>            
                        <div class="">
                            <p class="text-black-50 fs-7 mb-0"><?= $viewsTable->getAllViewCountByBlog($row->id) ?> <i class="fa-regular fa-eye fa-fw fs-8 text-black-50 ms-1"></i></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row py-3 overflow-y-scroll">
                        <h1 class="fs-6 text-dark" id="commentTitle">Comments</h1>
                        <!-- each comment start -->
                        <div class="col-12 px-0" id="comments_container">
                          <?php if(count($comments)): ?>
                            <?php foreach($comments as $comment): ?>
                                <div class="w-100 px-4 py-2 bg-light d-flex align-items-center justify-content-between my-1" id="commentElement<?= $comment->id ?>">
                                    <div class="d-flex align-items-start gap-3 w-100">
                                        <a href="./profile.php?u=<?= $comment->userUnisqueId ?>"><img src="./images/profiles/<?= $comment->profile ?>" class="comment_profile" alt=""></a>
                                        <div class="flex-grow-1">
                                            <div class="comment_content shadow-sm w-100 <?= ($comment->is_hidden) ? (($row->user_id == $_SESSION['auth']['id']) ? 'hiddenComment' : 'blockedComment') : '' ?>" id="comment<?= $comment->id ?>">
                                                <?php if($comment->user_id == $row->user_id): ?>
                                                    <small class="text-primary fs-8 mb-1 d-block"><i class="fa-solid fa-person"></i> Author</small>
                                                <?php endif; ?>
                                                <h5 class="fw-bold text-dark fs-7 mb-2"><?= $comment->username ?></h5>
                                                <small class="d-block fs-7 text-black-50 eachBlogContent" id="commentContent<?= $comment->id ?>"><?= ($comment->is_hidden) ? (($row->user_id == $_SESSION['auth']['id']) ? 'You hide this comment' : 'This comment is hidden by author' ) : $comment->content ?></small>
                                                <textarea onkeypress="(event.which == 10) ? document.querySelector('#updateComment<?= $comment->id ?>').click() : ''" class="form-control fs-7 text-black-50 d-none eachBlogContent" id="commentEditInput<?= $comment->id ?>"><?= ($comment->is_hidden) ? (($row->user_id == $_SESSION['auth']['id']) ? 'You hide this comment' : 'This comment is hidden by author' ) : $comment->content ?></textarea>
                                                <small id="notiCommentEdit<?= $comment->id ?>" class="fs-8 d-none ps-2">ctrl + enter to <a href="" id="updateComment<?= $comment->id ?>" onclick="updateComment(event, <?= $comment->id ?>)" class="text-primary fw-bold">save</a></small>
                                            </div>
                                            <div class="w-100 d-flex align-items-center justify-content-between mt-1 comment_actions_cont">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div id="appreciateBlogContainer<?= $comment->id ?>">
                                                        <?php if($comment->is_liked): ?>
                                                            <a href="" class="fs-8 fw-bold text-decoration-none text-primary" id="commentAppreciateStatus<?= $comment->id ?>" onclick="unAppreciateComment(event, <?= $comment->id ?>, <?= ($row->user_id==$_SESSION['auth']['id']) ? 1 : 0 ?>)">appreciated</a>
                                                        <?php else: ?>
                                                            <a href="" class="fs-8 fw-bold text-decoration-none text-black-50" id="commentAppreciateStatus<?= $comment->id ?>" onclick="appreciateComment(event, <?= $comment->id ?>, <?= ($row->user_id==$_SESSION['auth']['id']) ? 1 : 0 ?>)">appreciate</a>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <small class="fs-8 text-black-50"><?= Carbon::create($comment->created_at)->diffforHumans() ?></small>
                                                    </div>
                                                </div>
                                                <i class="fa-solid fa-ellipsis fa-fw ms-4 text-black-50 cursor-pointer fs-7" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                    <ul class="dropdown-menu" id="comment_menu<?= $comment->id ?>">
                                                        <?php if($row->user_id == $_SESSION['auth']['id']): ?>
                                                            <div id="hideMenuComment<?= $comment->id ?>">
                                                                <?php if($comment->is_hidden): ?>
                                                                    <li class="small"><a class="dropdown-item" href="#" onclick="unhideComment(event, <?= $comment->id ?>, <?= $row->user_id ?>)"><i class="fa-regular fa-rectangle-xmark me-2 fa-fw"></i> Unhide this comment</a></li>
                                                                <?php else: ?>
                                                                    <li class="small"><a class="dropdown-item" href="#" onclick="hideComment(event, <?= $comment->id ?>, <?= $row->user_id ?>)"><i class="fa-regular fa-rectangle-xmark me-2 fa-fw"></i> Hide this comment</a></li>
                                                                <?php endif; ?>
                                                            </div>
                                                            <li class="small"><a class="dropdown-item" href="/comment/remove" onclick="deleteComment(event, <?= $comment->id ?>, <?= $row->user_id ?>)"><i class="fa-solid fa-trash me-2 fa-fw"></i> remove</a></li>
                                                        <?php endif; ?>
                                                        <li class="small"><a class="dropdown-item" href="/comment/report" onclick="reportComment(event, <?= $comment->id ?>)"><i class="fa-regular fa-flag fa-fw me-2"></i> report</a></li>
                                                        <?php if($comment->user_id == $_SESSION['auth']['id'] && !$comment->is_hidden): ?>
                                                            <li class="small" id="editCommentBtn<?= $comment->id ?>"><a class="dropdown-item" href="/comment/edit" onclick="editComment(event, <?= $comment->id ?>)"><i class="fa-solid fa-pen me-2 fa-fw"></i> edit</a></li>
                                                        <?php endif; ?>
                                                    </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <p class="mb-0 fs-7 text-center" id="noCommentStatus">There's no comments yet.</p>
                          <?php endif; ?>
                        </div>
                        <!-- each comment end -->
                       
                    </div>
                    <div class="comment_inbox mx-auto gap-3 col-12 bg-light py-3 px-2">
                        <img src="./images/profiles/<?= $_SESSION['auth']['profile'] ?>" class="comment_profile" alt="">
                        <div class="comment_area flex-grow-1 gap-3">
                            <textarea name="" rows="1" class="comment_input fs-7 text-dark" id="comment_textarea" placeholder="Write a comment"></textarea>
                            <i class="fa-solid fa-paper-plane cursor-pointer text-black-50 mt-2 set_comment" id="commentBtn" onclick="commented(event, <?= $row->id ?>,<?= $row->user_id ?>)"></i>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <p class="text-center mb-0 text-black-50 small mt-3">No such blog is found.</p>
            <?php endif; ?>
        </div>
    </div>
   

    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/main.js"></script>
    <script>

        let alertToast = document.querySelector('#alertToast');
        let toastContent = document.querySelector('#toastContent');
        let toast = new bootstrap.Toast(alertToast);
        let showAppreciatesModal = new bootstrap.Modal(document.querySelector('#showAppreciatesModal'));
        let comments_container = document.querySelector('#comments_container');

        function init () {
            document.querySelector('#comment_textarea').focus();
        }

        function showBlogCover (event) {
            document.querySelector('#imageDetail').setAttribute('src', event.target.getAttribute('src'));
            document.querySelector('#biggerImage').classList.add('show');
       }

        function setViewed() {
            setTimeout(() => {
                let blogData = new FormData();
                blogData.append("blogId", <?= $row->id ?>);
                fetch('./_actions/handle_set_view_blog.php', {
                    method: 'POST',
                    body: blogData
                }).then(res => res.json())
                .then(data => {
                    if(data.status == 500) {
                        alert(data.message);
                        console.log(data.message);
                    }

                    if(data.status == 200) {
                        console.log('Set');
                    }
                })
            }, 10000)
        }

        document.querySelector('.backgroundBigger').addEventListener('click', function () {
            document.querySelector('#biggerImage').classList.remove('show');
        });

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

        function deleteBlog (event, id) {
            event.preventDefault();

            if(id !== undefined || id !== 0 || id !== null) {
               if(confirm("Are you sure to delete?")) {
                fetch("./_actions/handle_delete_blog.php?id=" + id).then(res => res.json())
                .then(data => {
                    if(data.status === 200) {
                        toastContent.textContent = "Blog deleted successfully";
                        toast.show();
                        location.href = "./index.php";
                    }

                    if(data.status === 404) {
                        toastContent.textContent = "No such blog is found";
                        toast.show();
                    }

                    if(data.status === 500) {
                        toastContent.textContent = "Something went wrong with the server. Try again";
                        toast.show();
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
                    document.querySelector('#blog_menu').innerHTML = ` <li class="small"><button class="dropdown-item" href="" onclick="removeFromFavorites(event, ${blogId})"><i class="fa-solid fa-bookmark me-2 fa-fw"></i> Unsave blog</button></li>`

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
                    document.querySelector('#blog_menu').innerHTML = `
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
                                        <a href=""><img src="./images/profiles/${user.profile}" class="blogProfile" alt="${user.usernamae}" /></a>
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

        // comment start
        function commented(event, blogId, blogUserId) {
            event.preventDefault();

            let comment_content = document.querySelector('#comment_textarea').value;
            if(comment_content.length) {
                let commentData = new FormData();
                commentData.append("content", comment_content);
                commentData.append("blogId", blogId);
                commentData.append("blogUserId", blogUserId);

                fetch('./_actions/handle_comment.php', {
                    method: 'POST',
                    body: commentData
                }).then(res => res.json())
                .then(data => {
                    if(data.status == 500) {
                        console.log(data.message);
                    }

                    if(data.status == 200) {
                        if(document.querySelector('#noCommentStatus')) {
                            document.querySelector('#noCommentStatus').remove();
                        }

                        comments_container.innerHTML += `
                            <div class="w-100 px-4 py-2 bg-light d-flex align-items-center justify-content-between my-1" id="commentElement${data.data.id}">
                                <div class="d-flex align-items-start gap-3 w-100">
                                    <img src="./images/profiles/<?= $_SESSION['auth']['profile'] ?>" class="comment_profile" alt="">
                                    <div class="flex-grow-1">
                                        <div class="comment_content shadow-sm w-100" id="comment${data.data.id}">
                                            <?php if($_SESSION['auth']['id'] == $row->user_id): ?>
                                                <small class="text-primary fs-8 mb-1 d-block"><i class="fa-solid fa-person"></i> Author</small>
                                            <?php endif; ?>
                                            <h5 class="fw-bold text-dark fs-7 mb-2"><?= $_SESSION['auth']['username'] ?></h5>
                                            <small class="d-block fs-7 text-black-50 eachBlogContent" id="commentContent${data.data.id}">${data.data.content}</small>
                                            <textarea onkeypress="(event.which == 10) ? document.querySelector('#updateComment${data.data.id}').click() : ''" class="form-control fs-7 text-black-50 d-none eachBlogContent" id="commentEditInput${data.data.id}">${data.data.content}</textarea>
                                                <small id="notiCommentEdit${data.data.id}" class="fs-8 d-none ps-2">ctrl + enter to <a href="" id="updateComment${data.data.id}" onclick="updateComment(event, ${data.data.id})" class="text-primary fw-bold">save</a></small>
                                        </div>
                                        <div class="w-100 d-flex align-items-center justify-content-between mt-1 comment_actions_cont">
                                            <div class="d-flex align-items-center gap-3">
                                                <div id="appreciateBlogContainer${data.data.id}">
                                                    <a href="" class="fs-8 fw-bold text-decoration-none text-black-50" id="commentAppreciateStatus${data.data.id}" onclick="appreciateComment(event, ${data.data.id},  <?= ($row->user_id==$_SESSION['auth']['id']) ? 1 : 0 ?>)">appreciate</a>
                                                </div>
                                                <div>
                                                    <small class="fs-8 text-black-50">${data.data.created_at}</small>
                                                </div>
                                            </div>
                                            <i class="fa-solid fa-ellipsis fa-fw text-black-50 cursor-pointer fs-7" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu" id="comment_menu${data.data.id}">
                                                <?php if($row->user_id == $_SESSION['auth']['id']): ?>
                                                    <div id="hideMenuComment${data.data.id}">
                                                        <li class="small"><a class="dropdown-item" href="/comment/hide" onclick="hideComment(event, ${data.data.id}, <?= $row->user_id ?>)"><i class="fa-regular fa-rectangle-xmark me-2 fa-fw"></i> Hide this comment</a></li>
                                                    </div>

                                                    <li class="small"><a class="dropdown-item" href="/comment/remove" onclick="deleteComment(event, ${data.data.id}, <?= $row->user_id ?>)"><i class="fa-solid fa-trash me-2 fa-fw"></i> remove</a></li>
                                                <?php endif; ?>
                                                    <li class="small"><a class="dropdown-item" href="/comment/report" onclick="reportComment(event, ${data.data.id})"><i class="fa-regular fa-flag fa-fw me-2"></i> report</a></li>
                                                    <li class="small" id="editCommentBtn${data.data.id}"><a class="dropdown-item" href="/comment/edit" onclick="editComment(event, ${data.data.id})"><i class="fa-solid fa-pen me-2 fa-fw"></i> edit</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        let prev = parseInt(document.querySelector('#commentCount').textContent);
                        document.querySelector('#commentCount').textContent = prev + 1;
                        document.querySelector('#comment_textarea').value = "";
                        document.querySelector('#comment_textarea').focus();
                        window.scrollTo(0, document.body.offsetHeight);


                        document.querySelector('#alertInfoContent').textContent = 'Commented';
                        document.querySelector('.alertInfo').classList.add('showAlert');

                        document.querySelector('.alertInfo').addEventListener('animationend', function (e) {
                            document.querySelector('.alertInfo').classList.remove('showAlert');
                        })

                    }
                })
            }
        }


        function appreciateComment(event, commentId, permission) {
            event.preventDefault();
            
            if(!permission) {
                alert('Only Author can control appreciation');
            } else {
                let commentData = new FormData();
                commentData.append('commentId', commentId);

                fetch('./_actions/handle_appreciate_comment.php', {
                    method: 'POST',
                    body: commentData
                }).then( res => res.json() )
                .then(data => {
                    if(data.status == 500) {
                        console.log(data.message);
                    }

                    if(data.status == 200) {
                        event.target.remove();
                        document.querySelector('#appreciateBlogContainer' +commentId).innerHTML = `
                            <a href="" class="fs-8 fw-bold text-decoration-none text-primary" id="commentAppreciateStatus${commentId}" onclick="unAppreciateComment(event, ${commentId}, <?= ($row->user_id==$_SESSION['auth']['id']) ? 1 : 0 ?>)">appreciated</a>
                        `

                        document.querySelector('#alertInfoContent').textContent = 'Commented appreciated';
                        document.querySelector('.alertInfo').classList.add('showAlert');

                        document.querySelector('.alertInfo').addEventListener('animationend', function (e) {
                            document.querySelector('.alertInfo').classList.remove('showAlert');
                        })
                    }
                })
            }
        }

        function unAppreciateComment(event, commentId, permission) {
            event.preventDefault();

            if(!permission) {
                alert('Only Author can control appreciation');
            } else {
                let commentData = new FormData();
                commentData.append('commentId', commentId);

                fetch('./_actions/handle_unappreciate_comment.php', {
                    method: 'POST',
                    body: commentData
                }).then(res => res.json())
                .then(data => {
                    if(data.status == 500) {
                        console.log(data.message);s
                    } 

                    if(data.status == 200) {
                        event.target.remove();
                        document.querySelector('#appreciateBlogContainer' +commentId).innerHTML = `
                        <a href="" class="fs-8 fw-bold text-decoration-none text-black-50" id="commentAppreciateStatus${commentId}" onclick="appreciateComment(event, ${commentId}, <?= ($row->user_id==$_SESSION['auth']['id']) ? 1 : 0 ?>)">appreciate</a>
                        `;

                        document.querySelector('#alertInfoContent').textContent = 'Comment unappreciated';
                        document.querySelector('.alertInfo').classList.add('showAlert');

                        document.querySelector('.alertInfo').addEventListener('animationend', function (e) {
                            document.querySelector('.alertInfo').classList.remove('showAlert');
                        })
                    }
                })
            }
        }

        function deleteComment (event, commentId, blogOwnerId) {
            event.preventDefault();

            if(confirm("Are you sure to remove this comment?")) {
                if(commentId != null || commentId != undefined) {
                let commentData = new FormData();
                commentData.append('commentId', commentId);

                fetch('./_actions/handle_remove_comment.php', {
                    method: 'POST',
                    body: commentData
                }).then(res => res.json())
                .then(data => {
                    if(data.status == 500) {
                        console.log(data.message);
                    }

                    if(data.status == 200) {
                        let prev = parseInt(document.querySelector('#commentCount').textContent);

                        document.querySelector('#commentElement' + commentId).remove();
                        document.querySelector('#commentCount').textContent = prev - 1;

                        document.querySelector('#alertInfoContent').textContent = 'Comment removed';
                        document.querySelector('.alertInfo').classList.add('showAlert');

                        document.querySelector('.alertInfo').addEventListener('animationend', function (e) {
                            document.querySelector('.alertInfo').classList.remove('showAlert');
                        })
                    }
                })
            }
            }
        }

        function hideComment (event, commentId, blogOwnerId) {
            event.preventDefault();
            if(commentId != null || commentId != undefined) {
                let commentData = new FormData();
                commentData.append('commentId', commentId);

                fetch('./_actions/handle_hide_comment.php', {
                    method: 'POST',
                    body: commentData
                }).then(res => res.json())
                .then(data => {
                    if(data.status == 500) {
                        console.log(data.message);
                    }

                    if(data.status == 200) {
                        event.target.remove();
                        if(document.querySelector('#editCommentBtn' + commentId)) {
                            document.querySelector('#editCommentBtn' + commentId).remove();
                        }
                        document.querySelector('#hideMenuComment' + commentId).innerHTML = `
                            <li class="small"><a class="dropdown-item" href="#" onclick="unhideComment(event, ${commentId}, ${blogOwnerId})"><i class="fa-regular fa-rectangle-xmark me-2 fa-fw"></i> Unhide this comment</a></li>
                        `;
                        // remove classes
                        document.querySelector('#commentEditInput' + commentId).classList.add('d-none');
                        document.querySelector('#commentEditInput' + commentId).classList.remove('d-block');
                        document.querySelector('#notiCommentEdit' + commentId).classList.add('d-none');
                        document.querySelector('#notiCommentEdit' + commentId).classList.remove('d-block');

                        document.querySelector('#commentContent' + commentId).classList.add('d-block');
                        document.querySelector('#commentContent' + commentId).classList.remove('d-none');

                        document.querySelector('#comment' + commentId).classList.remove('hiddenComment');
                        document.querySelector('#comment' + commentId).classList.remove('blockedComment');

                        // add classes
                        document.querySelector('#comment' + commentId).classList.add("hiddenComment");
                        //content
                        document.querySelector('#commentContent' + commentId).textContent = "<?= ($row->user_id == $_SESSION['auth']['id']) ? 'You hide this comment' : 'This comment is hidden by author' ?>";
                        document.querySelector('#commentEditInput' + commentId).value = "<?= ($row->user_id == $_SESSION['auth']['id']) ? 'You hide this comment' : 'This comment is hidden by author' ?>";

                        document.querySelector('#alertInfoContent').textContent = 'Comment hidden';
                        document.querySelector('.alertInfo').classList.add('showAlert');

                        document.querySelector('.alertInfo').addEventListener('animationend', function (e) {
                            document.querySelector('.alertInfo').classList.remove('showAlert');
                        })
                    }
                });
            }
        }

        
        function unhideComment (event, commentId, blogOwnerId) {
            event.preventDefault();

            if(commentId != null || commentId != undefined) {
                let commentData = new FormData();
                commentData.append('commentId', commentId);

                fetch('./_actions/handle_unhide_comment.php', {
                    method : 'POST',
                    body: commentData
                }).then(res => res.json())
                .then(data => {
                    if(data.status === 500) {
                        console.log(data.message);
                    }

                    if(data.status == 200) {
                        event.target.remove();
                        document.querySelector('#hideMenuComment' + commentId).innerHTML = `
                            <li class="small"><a class="dropdown-item" href="#" onclick="hideComment(event, ${commentId}, ${blogOwnerId})"><i class="fa-regular fa-rectangle-xmark me-2 fa-fw"></i> Hide this comment</a></li>
                        `;
                         // remove classes
                        document.querySelector('#comment' + commentId).classList.remove('hiddenComment');
                        document.querySelector('#comment' + commentId).classList.remove('blockedComment');

                        document.querySelector('#commentContent' + commentId).textContent = data.comment.content;
                        document.querySelector('#commentEditInput' + commentId).value = data.comment.content;

                        if(data.comment.user_id == <?= $_SESSION['auth']['id'] ?>) {
                            document.querySelector('#comment_menu' + data.comment.id).innerHTML += `
                                <li class="small" id="editCommentBtn${data.comment.id}"><a class="dropdown-item" href="/comment/edit" onclick="editComment(event, ${data.comment.id})"><i class="fa-solid fa-pen me-2 fa-fw"></i> edit</a></li>
                            `;
                        }

                        document.querySelector('#alertInfoContent').textContent = 'Comment undo hidden';
                        document.querySelector('.alertInfo').classList.add('showAlert');

                        document.querySelector('.alertInfo').addEventListener('animationend', function (e) {
                            document.querySelector('.alertInfo').classList.remove('showAlert');
                        })
                    }
                })
            }
        }


        function editComment (event, commentId) {
            event.preventDefault();
            document.querySelector('#commentContent' + commentId).classList.remove('d-block');
            document.querySelector('#commentContent' + commentId).classList.add('d-none');

            document.querySelector('#commentEditInput' + commentId).classList.remove('d-none');
            document.querySelector('#commentEditInput' + commentId).classList.add('d-block');
            document.querySelector('#notiCommentEdit' + commentId).classList.remove('d-none');
            document.querySelector('#notiCommentEdit' + commentId).classList.add('d-block');

            document.querySelector('#commentEditInput' + commentId).select();
            document.querySelector('#commentEditInput' + commentId).focus();

        }


        function updateComment(event, commentId) {
            event.preventDefault();

            let updatedContent = document.querySelector('#commentEditInput' + commentId).value;
            if(updatedContent.length) {
                let updatedCommentData = new FormData();
                updatedCommentData.append('content', updatedContent);
                updatedCommentData.append('commentId', commentId);

                fetch('./_actions/handle_update_comment.php', {
                    method: 'POST',
                    body: updatedCommentData
                }).then(res => res.json())
                .then(data => {
                    if(data.status == 200) {
                        // class change
                        document.querySelector('#commentEditInput' + commentId).classList.add('d-none');
                        document.querySelector('#commentEditInput' + commentId).classList.remove('d-block');
                        document.querySelector('#notiCommentEdit' + commentId).classList.add('d-none');
                        document.querySelector('#notiCommentEdit' + commentId).classList.remove('d-block');

                        document.querySelector('#commentContent' + commentId).classList.add('d-block');
                        document.querySelector('#commentContent' + commentId).classList.remove('d-none');


                        // content change
                        document.querySelector('#commentEditInput' + commentId).value = updatedContent;
                        document.querySelector('#commentContent' + commentId).textContent = updatedContent;

                        document.querySelector('#alertInfoContent').textContent = 'Comment edited';
                        document.querySelector('.alertInfo').classList.add('showAlert');

                        document.querySelector('.alertInfo').addEventListener('animationend', function (e) {
                            document.querySelector('.alertInfo').classList.remove('showAlert');
                        })
                    } 

                    if(data.status == 500) {
                        console.log(data.message);
                    }
                })
            }
        }

        function reportComment(event, commentId) {
            event.preventDefault();

            if(confirm("Are you sure to report this comment?")) {
                if(commentId != null || commentId != undefined) {
                    let commentData = new FormData();
                    commentData.append('commentId', commentId);

                    fetch('./_actions/handle_report_comment.php', {
                        method: 'POST',
                        body: commentData
                    }).then(res => res.json())
                    .then(data => {
                        if(data.status == 500) {
                            console.log(data.message);
                        }

                        if(data.status == 200) {
                            if(data.comment_status == 4) {
                                document.querySelector('#commentElement' + commentId).remove();
                                let prev = parseInt(document.querySelector('#commentCount').textContent);
                                document.querySelector('#commentCount').textContent = prev - 1;

                                document.querySelector('#alertInfoContent').textContent = 'Comment deleted by too many reports';
                                document.querySelector('.alertInfo').classList.add('showAlert');

                                document.querySelector('.alertInfo').addEventListener('animationend', function (e) {
                                    document.querySelector('.alertInfo').classList.remove('showAlert');
                                })
                            }

                            if(data.comment_status == 1) {
                                document.querySelector('#alertInfoContent').textContent = 'Comment Reported';
                                document.querySelector('.alertInfo').classList.add('showAlert');

                                document.querySelector('.alertInfo').addEventListener('animationend', function (e) {
                                    document.querySelector('.alertInfo').classList.remove('showAlert');
                                })
                            }
                        }
                    });
                }
            }
        }
        // comment end

        init();
        setViewed();
        
    </script>
</body>
</html>