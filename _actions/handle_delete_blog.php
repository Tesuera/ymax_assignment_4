<?php 
date_default_timezone_set("Asia/Yangon");

include "../vendor/autoload.php";
use Lib\Database\MySQL;
use Lib\Tables\BlogsTable;

if(isset($_GET['id'])) {
    $blogsTable = new BlogsTable(new MySQL());

    $row = $blogsTable->deleteBlog($_GET['id']);
    
    if($row) {
        echo json_encode([
            'status' => 200,
            'message' =>  'Blog removed'
        ]);
        exit();
    }

    echo json_encode([
        'status' => 500,
        'message' => 'Internal Server error'
    ]);
}

echo json_encode([
    'status' => 404,
    'message' => 'Blog not found'
]);