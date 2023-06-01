<?php 
date_default_timezone_set("Asia/Yangon");

include "../vendor/autoload.php";
use Lib\Database\MySQL;
use Lib\Tables\BlogsTable;

if(isset($_GET['id'])) {
    $blogsTable = new BlogsTable(new MySQL());

    $count= $blogsTable->removeCover($_GET['id']);
    if($count) {
        echo json_encode([
            'status' => 200,
            'message' => 'Cover removed'
        ]);
        exit();
    } else {
        echo json_encode([
            'status' => 400,
            'message' => 'Already removed'
        ]);
        exit();
    }
}

echo json_encode([
    'status' => 404,
    'message' => 'No such blog is found'
]);
exit();