<?php

use Lib\Helpers\Auth;
use Lib\Database\MySQL;
use Lib\Tables\FavoritesTable;
session_start();

include "../vendor/autoload.php";

$auth = new Auth();
$auth->is_authenticated();

if(isset($_POST['blogId'])) {
    $favoritesTable = new FavoritesTable(new MySQL());

    if(!$favoritesTable->checkingStatus($_POST['blogId'], $_SESSION['auth']['id'])) {
        echo json_encode([
            'status' => 400,
            'message' => "Can't unsave. You need to save that blog first"
        ]);
        exit();
    } else {
        $result = $favoritesTable->removeFromFavorite($_POST['blogId'], $_SESSION['auth']['id']);

        if($result) {
            echo json_encode([
                'status' => 200,
                'message' => "Unsaved"
            ]);
        }
    }
}