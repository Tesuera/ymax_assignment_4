<?php

namespace Lib\Tables;

use PDOException;
use Lib\Database\MySQL;

class CommentsTable {
    private $db;

    public function __construct (MySQL $db) {
        $this->db = $db->connection();
    }

    public function addComment ($data) {
        try {
            if(isset($data['by_author'])) {
                $statement = $this->db->prepare("insert into comments (blog_id, user_id, content, is_hidden, is_liked, by_author) values (:blog_id, :user_id, :content, :is_hidden, :is_liked, :by_author)");
            } else {
                $statement = $this->db->prepare("insert into comments (blog_id, user_id, content, is_hidden, is_liked) values (:blog_id, :user_id, :content, :is_hidden, :is_liked)");
            }

            $statement->execute($data);
            $id = $this->db->lastInsertId();
            return $id;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function commentsCountByBlog ($blogId) {
        try {
            $statement = $this->db->prepare("select COUNT(id) as count from comments where blog_id=:blog_id");
            $statement->execute(['blog_id' => $blogId]);
    
            $result = $statement->fetch();
    
            return $result->count;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function getAllCommentsByBlog ($blogId) {
        try {
            $statement = $this->db->prepare("select c.*, u.profile, u.username, u.email, u.unique_id as userUnisqueId from comments c inner join users u on c.user_id=u.id where c.blog_id=:blog_id order by is_hidden asc, id desc");
            $statement->execute([
                'blog_id' => $blogId
            ]);
            $rows = $statement->fetchAll();
            return $rows;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function getComment ($commentId) {
        try {
            $statement = $this->db->prepare("select * from comments where id=:comment_id");
            $statement->execute([
                'comment_id' => $commentId
            ]);
            $rows = $statement->fetch();
            return $rows;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function permission ($userId, $commentId) {
        try {
            $statement = $this->db->prepare("select u.id as blog_id from comments c inner join blogs b on c.blog_id=b.id inner join users u on b.user_id=u.id where c.id=:comment_id");
            $statement->execute([
                'comment_id' => $commentId
            ]);
            $res = $statement->fetch();
            if($res->blog_id == $userId) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function appreciateComment ($commentId) {
        try {
            $statement = $this->db->prepare("update comments set is_liked=1 where id=:comment_id");
            $statement->execute([
                'comment_id' => $commentId
            ]);

            $row = $statement->rowCount();
            return $row;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function unappreciateComment ($commentId) {
        try {   
            $statement = $this->db->prepare("update comments set is_liked=0 where id=:comment_id");
            $statement->execute([
                'comment_id' => $commentId
            ]);
            $row = $statement->rowCount();
            return $row;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function hideComment ($commentId) {
        try {
            $statement = $this->db->prepare("update comments set is_hidden=1 where id=:comment_id");
            $statement->execute([
                'comment_id' => $commentId
            ]);
            $row = $statement->rowCount();
            return $row;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function unHideComment ($commentId) {
        try {
            $statement = $this->db->prepare("update comments set is_hidden=0 where id=:comment_id");
            $statement->execute([
                'comment_id' => $commentId
            ]);
            $row = $statement->rowCount();
            return $row;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function removeComment ($commentId) {
        try {
            $statement = $this->db->prepare("delete from comments where id=:comment_id");
            $statement->execute([
                'comment_id' => $commentId,
            ]);

            $row = $statement->rowCount();
            return $row;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function is_commented_user($userId, $commentId) {
        try {
            $statement = $this->db->prepare("select user_id from comments where id=:comment_id");
            $statement->execute([
                'comment_id' => $commentId
            ]);
            $row = $statement->fetch();
            if($row->user_id != $userId) {
                return false;
            }
            return true;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function updateComment ($data) {
        try {
            $statement = $this->db->prepare("update comments set content=:content, modified_at=:modified_at where id=:comment_id");
            $statement->execute($data);
            $row = $statement->rowCount();
            return $row;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }
}