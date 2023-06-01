<?php

namespace Lib\Tables;

use PDOException;
use Lib\Database\MySQL;

class ReportsCommentsTable {
    private $db;

    public function __construct (MySQL $db) {
        $this->db = $db->connection();
    }

    public function reportComment ($commentId, $userId) {
        try {
            $statement = $this->db->prepare("insert into reports (user_id, comment_id) values (:user_id, :comment_id)");
            $statement->execute([
                'user_id' => $userId,
                'comment_id' => $commentId,
            ]);
            $result = $this->db->lastInsertId();

            $statement = $this->db->prepare("select COUNT(id) as counts from reports where comment_id=:comment_id");
            $statement->execute([
                'comment_id' => $commentId
            ]);
            $count = $statement->fetch();

            if($count->counts >= 10) {
                $statement = $this->db->prepare("delete from comments where id=:comment_id");
                $statement->execute([
                    'comment_id' => $commentId
                ]);
                $effectedRow = $statement->rowCount();

                $statement = $this->db->prepare("delete from reports where comment_id=:comment_id");
                $statement->execute([
                    'comment_id' => $commentId
                ]);
                $rowCount = $statement->rowCount();

                return [
                    'status' => 4
                ];
            }
            return [
                'status' => 1
            ];
        } catch(PDOException $error) {
            $error->getMessage();
            exit();
        } 
    } 
}