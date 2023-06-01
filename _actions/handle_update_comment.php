<?php
date_default_timezone_set("Asia/Yangon");
use Carbon\Carbon;
use Lib\Database\MySQL;
use Lib\Tables\CommentsTable;

session_start();
include "../vendor/autoload.php";

if(!empty($_POST['content'])) {
    $commentsTable = new CommentsTable(new MySQL());
    $permission = $commentsTable->is_commented_user($_SESSION['auth']['id'], $_POST['commentId']);
    if($permission) {
        $data = [
            'content' => $_POST['content'],
            'comment_id' => $_POST['commentId'],
            'modified_at' => Carbon::now()
        ];
        $result = $commentsTable->updateComment($data);
        if($result) {
            echo json_encode([
                'status' => 200,
                'message' => 'Comment updated'
            ]);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Bad request'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 500,
            'message' => 'Bad request'
        ]);
    }
} else {
    echo json_encode([
        'status' => 500,
        'message' => 'Bad request'
    ]);
}