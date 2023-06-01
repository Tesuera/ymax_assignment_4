<?php 
session_start();

include "../vendor/autoload.php";
use Lib\Helpers\Auth;
use Lib\Database\MySQL;
use Lib\Tables\FavoritesTable;

$auth = new Auth();
$auth->is_authenticated();


if(isset($_POST['blogId'])) {
    $favoritesTable = new FavoritesTable(new MySQL());

    if($favoritesTable->checkingStatus($_POST['blogId'], $_SESSION['auth']['id'])) {
        echo json_encode([
            'status' => 400,
            'message' => "You've already saved that blog"
        ]);
        exit();
    } else {
        $result = $favoritesTable->addToFavorite ($_POST['blogId'], $_SESSION['auth']['id']);

        if($result) {
            echo json_encode([
                'status' => 200,
                'message' => 'Saved'
            ]);
        }
    }
}