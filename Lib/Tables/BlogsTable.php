<?php

namespace Lib\Tables;

use PDOException;
use Carbon\Carbon;
use Lib\Database\MySQL;

class BlogsTable {
    private $db;

    public function __construct (MySQL $db) {
        $this->db = $db->connection();
    }


    public function createBlog ($data) {
        try {
            if(isset($data['cover'])) {
                $statement = $this->db->prepare("insert into blogs (unique_id, user_id, title, content, cover, is_removed, modified_at) values (:unique_id, :user_id, :title, :content, :cover, :is_removed, :modified_at)");
            } else {
                $statement = $this->db->prepare("insert into blogs (unique_id, user_id, title, content, is_removed, modified_at) values (:unique_id, :user_id, :title, :content, :is_removed, :modified_at)");
            }
    
            $statement->execute($data);
            $lastInsertId = $this->db->lastInsertId();
    
            if($lastInsertId) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function getAllBlogs () {
        try {
            $loginId = $_SESSION['auth']['id'];
            $statement = $this->db->prepare("select blog.*, user.unique_id as userUniqueId, user.username as userName, user.email as userEmail, user.profile as userProfile, user.created_at as joinDate from blogs blog inner join users user on blog.user_id = user.id ORDER BY created_at DESC");

            $statement->execute();
            $results = $statement->fetchAll();
            return $results;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function getBlogDetail ($unique_id) {
        try {
            $statement = $this->db->prepare("select blog.*, user.unique_id as userUniqueId, user.username as userName, user.email as userEmail, user.profile as userProfile, user.created_at as joinDate from blogs blog inner join users user on blog.user_id = user.id where blog.unique_id='$unique_id' ORDER BY created_at DESC");
            $statement->execute();
            $row = $statement->fetch();
    
            return $row;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function deleteBlog ($id) {
        try {
            $statement = $this->db->prepare("delete from blogs where id=$id");
            $statement->execute();
    
            $row = $statement->rowCount();
            return $row;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function getBlog ($unique_id) {
        try {
            $statement = $this->db->prepare("select * from blogs where unique_id='$unique_id'");
            $statement->execute();
            $row =$statement->fetch();

            return $row;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function updateBlog ($data) {
        try {
            if(isset($data['cover'])) {
                $statement = $this->db->prepare("update blogs set title=:title, content=:content, cover=:cover, modified_at=:modified_at where unique_id=:unique_id");
            } else {
                $statement = $this->db->prepare("update blogs set title=:title, content=:content, modified_at=:modified_at where unique_id=:unique_id");
            }

            $statement->execute($data);
            $count = $statement->rowCount();

            return $count;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function removeCover ($blog_id) {
        try {
            $statement = $this->db->prepare("update blogs set cover=NULL, modified_at=:modified_at where unique_id = :unique_id");
            $statement->execute([
                'modified_at' => Carbon::now(),
                'unique_id' => $blog_id
            ]);
            $row = $statement->rowCount();

            return $row;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }
}