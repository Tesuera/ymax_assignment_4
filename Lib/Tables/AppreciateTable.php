<?php

namespace Lib\Tables;

use PDOException;
use Lib\Database\MySQL;

class Appreciatetable {
    private $db;

    public function __construct (MySQL $db) {
        $this->db = $db->connection();
    } 

    public function appreciate ($userId, $blogId) {
        try {
            $statement = $this->db->prepare("insert into appreciates (blog_id, user_id) values (:blog_id, :user_id)");
            $statement->execute([
                'blog_id' => $blogId,
                'user_id' => $userId
            ]);
            $lastId = $this->db->lastInsertId();
            return $lastId;

        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function undoAppreciate ($userId, $blogId) {
        try {
            $statement = $this->db->prepare("delete from appreciates where blog_id=:blog_id and user_id=:user_id");
            $statement->execute([
                'blog_id' => $blogId,
                'user_id' => $userId
            ]);
            $count = $statement->rowCount();
            return $count;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function checkingStatus ($userId, $blogId) {
        try {
            $statement = $this->db->prepare("select id from appreciates where user_id=:user_id and blog_id=:blog_id");
            $statement->execute([
                'user_id' => $userId,
                'blog_id' => $blogId
            ]);
            $result = $statement->rowCount();
            return $result;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function getAllAppreciatesByLoginUser () {
        try {
            $statement = $this->db->prepare("select blog_id from appreciates where user_id=:user_id");
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

    public function appreciatesCountByBlog ($blogId) {
        try {
            $statement = $this->db->prepare("select COUNT(id) as count from appreciates where blog_id=:blog_id");
            $statement->execute(['blog_id' => $blogId]);
    
            $result = $statement->fetch();
    
            return $result;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function getAllAppreciatesByUser ($blogId) {
        try {
            $statement = $this->db->prepare("select u.* from appreciates a left join users u on a.user_id = u.id where blog_id=:blog_id order by a.id desc");
            $statement->execute([
                'blog_id' => $blogId
            ]);
            $results = $statement->fetchAll();        

            return $results;
        } catch(PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

}