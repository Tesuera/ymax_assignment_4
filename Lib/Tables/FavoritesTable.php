<?php 

namespace Lib\Tables;

use PDOException;
use Lib\Database\MySQL;


class FavoritesTable {
    private $db;

    public function __construct(MySQL $db) {
        $this->db = $db->connection();
    }

    public function addToFavorite ($blogId, $userId) {
        try {
            $statement = $this->db->prepare("insert into favorites (blog_id, user_id) values (:blog_id, :user_id)");
            $statement->execute([
                "blog_id" => $blogId,
                "user_id" => $userId
            ]);

            $id = $this->db->lastInsertId();

            return $id;

        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function removeFromFavorite ($blogId, $userId) {
        try {
            $statement = $this->db->prepare("delete from favorites where blog_id=:blog_id and user_id=:user_id");
            $statement->execute([
                "blog_id" => $blogId,
                "user_id" => $userId
            ]);

            $count = $statement->rowCount();
            return $count;
            
        } catch(PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function checkingStatus ($blogId, $userId) {
        try {
            $statement = $this->db->prepare("select id from favorites where blog_id=:blog_id and user_id=:user_id");
            $statement->execute([
                "blog_id" => $blogId,
                "user_id" => $userId
            ]);
            $result = $statement->rowCount();

            return $result;

        } catch(PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function getAllFavoritesByLoginUser () {
        try {
            $statement = $this->db->prepare("select blog_id from favorites where user_id = :user_id");
            $statement->execute([
                'user_id' => $_SESSION['auth']['id']
            ]);

            $rows = $statement->fetchAll();
            return $rows;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function getFavoritesBlogs ($userId) {
        try {
            $statement = $this->db->prepare("select b.*, u.username as blogOwner from blogs b inner join users u on u.id=b.user_id where b.id in ((select blog_id from favorites f where f.user_id=:user_id));");
            $statement->execute([
                'user_id' => $userId
            ]);

            $rows = $statement->fetchAll();
            return $rows;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }
}