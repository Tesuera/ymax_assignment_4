<?php 

use Lib\Database\MySQL;
use Lib\Tables\CommentsTable;

session_start();
include "../vendor/autoload.php";

if(!empty($_POST['commentId'])) {
    $commentsTable = new CommentsTable(new MySQL());
    $permission = $commentsTable->permission($_SESSION['auth']['id'], $_POST['commentId']);

    if($permission) {
        $result = $commentsTable->unHideComment($_POST['commentId']);
        if($result) {
            $comment = $commentsTable->getComment($_POST['commentId']);
            echo json_encode([
                'status' => 200,
                'message' => 'Comment undo hidden',
                'comment' => $comment
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