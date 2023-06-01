<?php 
date_default_timezone_set("Asia/Yangon");

use Carbon\Carbon;
use Lib\Helpers\HTTP;
use Lib\Database\MySQL;
use Lib\Tables\BlogsTable;
$error_status = 0;
$errors = [];

if(isset($_POST['title']) && !empty($_POST['id'])) {
    $title;
    $content;
    $cover;

    $blogsTable = new BlogsTable(new MySQL());

    if(empty($_POST['title'])) {
        $error_status = 1;
        $errors['title'] = "Title is required";
    } else if(strlen($_POST['title']) < 2) {
        $error_status = 1;
        $errors['title'] = "Title must have at least 2 characters";
    } else if(strlen($_POST['title']) > 500) {
        $error_status = 1;
        $errors['title'] = "Title must not be longer than 50 characters";
    } else {
        $title = $_POST['title'];
    }

    if(empty($_POST['content'])) {
        $error_status  = 1;
        $errors['content'] = "Content is required";
    } else if(strlen($_POST['content']) < 10) {
        $error_status = 1;
        $errors['content'] = "Content must have at least 10 characters";
    } else if(strlen( $_POST['content']) > 100000) {
        $error_status = 1;
        $errors['content'] = "Content must not be longer than 100000 characters";
    } else {
        $content = $_POST['content'];
    }


    if(!empty($_POST['cover'])) {
        $folderPath = 'images/covers/';
        $image_parts = explode(";base64,", $_POST['cover']);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);

        $name =  uniqid() . '_covers_' . uniqid() . '.png';

        $file = $folderPath . $name;
        $cover = $name;
        file_put_contents($file, $image_base64);
    }

    if(!$error_status) {
        $unique_id = uniqid() . "_cover_id_" . uniqid();

        if(!empty($cover)) {
            $data = [
                'title' => $title,
                'content' => $content,
                'cover' => $cover,
                'unique_id' => $_POST['id'],
                'modified_at' => Carbon::now()
            ];
        } else {
            $data = [
                'title' => $title,
                'content' => $content,
                'unique_id' => $_POST['id'],
                'modified_at' => Carbon::now()
            ];
        }

        $count = $blogsTable->updateBlog($data);

        if($count) {
            HTTP::redirect('blog_detail.php?b=' . $_POST['id']);
        }
    }
}
