<?php

namespace Lib\Tables;

use PDOException;
use Lib\Database\MySQL;

class ViewsTable {
    private $db;

    public function __construct(MySQL $db) {
        $this->db = $db->connection();
    }

    public function setView($blogId, $userId) {
        try {
            $statement = $this->db->prepare("insert into views (blog_id, user_id) values (:blog_id, :user_id)");
            $statement->execute([
                'blog_id' => $blogId,
                'user_id' => $userId
            ]);
    
            $index = $this->db->lastInsertId();
            if($index) {
                return 1;
            }
            return 0;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function checkingStatus ($blogId, $userId) {
        try {
            $statement = $this->db->prepare("select id from views where blog_id=:blog_id and user_id=:user_id");
            $statement->execute([
                'blog_id' => $blogId,
                'user_id' => $userId
            ]);
            $results = $statement->fetchAll();
    
            return $results;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function getAllViewCountByBlog ($blogId) {
        try {
            $statement = $this->db->prepare("select COUNT(id) as count from views where blog_id=:blog_id");
            $statement->execute([
                'blog_id' => $blogId
            ]);
            $viewCount = $statement->fetch();
            return $viewCount->count;

        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }
}