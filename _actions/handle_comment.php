<?php

use Carbon\Carbon;
use Lib\Database\MySQL;
use Lib\Tables\CommentsTable;

session_start();

include "../vendor/autoload.php";

if(isset($_POST['blogId'])) {
    $commentsTable = new CommentsTable(new MySQL());

    $error_status = 0;

    if(empty($_POST['content'])) {
        $error_status = 1;
    }

    if(!$error_status) {
        $data = [
            'blog_id' => $_POST['blogId'],
            'user_id' => $_SESSION['auth']['id'],
            'content' => $_POST['content'],
            'is_hidden' => 0,
            'is_liked' => 0,
        ];

        if($_POST['blogUserId'] == $_SESSION['auth']['id']) {
            $data['by_author'] = 1;
        }

        $result = $commentsTable->addComment($data);
        if($result) {
            echo json_encode([
                'status' => 200,
                'message' => "commented",
                'data' => [
                    'id' => $result,
                    'blog_id' => $_POST['blogId'],
                    'user_id' => $_SESSION['auth']['id'],
                    'content' => $_POST['content'],
                    'is_hidden' => 0,
                    'is_liked' => 0,
                    'created_at' => Carbon::now()->diffforHumans()
                ]
            ]);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Something went wrong with the server'
            ]);
        }   
    }
}