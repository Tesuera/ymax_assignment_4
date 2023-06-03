<?php

namespace Lib\Tables;

use Lib\Database\MySQL;
use PDOException;

class UsersTable {

    private $db;

    public function __construct(MySQL $db) {
        $this->db = $db->connection();
    }

    public function register ($data) {
        try {
            if(isset($data['profile'])) {
                $statement = $this->db->prepare("insert into  users (unique_id, username, email, password, profile, modified_at) values (:unique_id, :username, :email, :password, :profile, :modified_at)");
            } else {
                $statement = $this->db->prepare("insert into  users (unique_id, username, email, password, modified_at) values (:unique_id, :username, :email, :password, :modified_at)");
            }
            $statement->execute($data);

            $lastIndex = $this->db->lastInsertId();
            if($lastIndex) {
                $statement = $this->db->prepare("select * from users where id=$lastIndex");
                $statement->execute();
                return $statement->fetch();
            }
            return $lastIndex;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function login ($data) {
        $statement = $this->db->prepare("select * from users where email = :email");
        $statement->execute([
            'email' => $data['email']
        ]);
        $user = $statement->fetch();
        if(isset($user->id)) {
            if(password_verify($data['password'], $user->password)) {
                return [
                    "status" => 200,
                    "message" => "Log in successfully",
                    "auth" => $user
                 ];
            } else {
                return [
                    "status" => 400,
                    "message" => "Incorrect email or password"
                ];
            }
        } else {
            return [
                "status" => 400,
                "message" => "Incorrect email or password"
            ];
        }
    }

    public function email_exist ($email) {
        try {
            $statement = $this->db->prepare("select count(id) as count from users where email = \"$email\" ");

            $statement->execute();
            $result = $statement->fetchAll();
    
            return $result[0]->count;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function getUser ($userId) {
        try {
            $statement = $this->db->prepare("select u.*, COUNT(distinct b.id) as blog_count, COUNT(a.id) as appreciate_count from users u left join blogs b on u.id=b.user_id left join appreciates a on b.id=a.blog_id where u.unique_id=:user_id");
            $statement->execute([
                'user_id' => $userId
            ]);
            $result = $statement->fetch();
            return $result;
        } catch(PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function getBlogsCountByUser ($userId) {
        try {
            $statement = $this->db->prepare("select COUNT(id) as blog_count from blogs where user_id=:user_id");
            $statement->execute([
                'user_id' => $userId
            ]);
            $result = $statement->fetch();
            return $result->blog_count;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function getAppreciatesCountByUser ($userId) {
        try {
            $statement = $this->db->prepare("select count(a.id) as appreciate_count from appreciates a inner join blogs b on a.blog_id=b.id where b.user_id=:user_id");

            $statement->execute([
                'user_id' => $userId
            ]);
            $result = $statement->fetch();
            return $result->appreciate_count;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function getPasswordByUser ($userId) {
        try {
            $statement = $this->db->prepare("select password from users where id=:user_id");
            $statement->execute([
                'user_id' => $userId
            ]);
            $result = $statement->fetch();
            return $result->password;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function changePassword ($newPassword, $userId) {
        try {
            $statement = $this->db->prepare("update users set password=:password where id=:user_id");
            $statement->execute([
                'password' => $newPassword,
                'user_id' => $userId
            ]);
            $rowCount = $statement->rowCount();
            return $rowCount;
        } catch(PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function updateProfile ($data) {
        try {
            if(isset($data['profile'])) {
                $statement = $this->db->prepare("update users set username=:username, email=:email, modified_at=:modified_at, profile=:profile where id=:user_id");
            } else {
                $statement = $this->db->prepare("update users set username=:username, email=:email, modified_at=:modified_at where id=:user_id");
            }
            $statement->execute($data);
            $result = $statement->rowCount();

            $statement = $this->db->prepare("select * from users where id=:user_id");
            $statement->execute([
                'user_id' => $data['user_id']
            ]);
            $row = $statement->fetch();
            return $row;
        } catch (PDoException $error) {
            echo $error->getMessage();
            exit();
        }
    }

    public function removeProfile ($userId) {
        try {
            $statement = $this->db->prepare("select profile from users where id=:user_id");
            $statement->execute([
                'user_id' => $userId
            ]);
            $result = $statement->fetch();
            if($result->profile != 'default.jpg') {
                unlink("../images/profiles/" . $result->profile);
            };

            $statement = $this->db->prepare("update users set profile='default.jpg' where id=:user_id");
            $statement->execute([
                'user_id' => $userId
            ]);
            $result = $statement->rowCount();
            return $result;
        } catch (PDOException $error) {
            echo $error->getMessage();
            exit();
        }
    }
}