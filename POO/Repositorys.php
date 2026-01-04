<?php

require_once 'Interfaces.php';
require_once 'database.php';

class UserRepository implements UserRepositoryInterface {
    private Database $db ;

    public function __construct(Database $conn){
        $this->db = $conn;
    }

    public function findByEmail(string $email) : User {
        $sql = "select * from User where email = :email";

        $query = $this->db->getConnection()->prepare($sql);
        $query->bindParam(":email" , $email);

        if($query->execute()) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;

            $id = $row['id_user'];

            if ($row['role'] === 'admin') {
                $extra = $this->getExtraData('Admin', $id);
                return new Admin(array_merge($row, $extra));
            } 
            
            if ($row['role'] === 'pro') {
                $extra = $this->getExtraData('Pro_User', $id);
                return new ProUser(array_merge($row, $extra));
            }

            if ($row['role'] === 'basic') {
                $extra = $this->getExtraData('Basic_User', $id);
                return new BasicUser(array_merge($row, $extra));
            }


            return null;
        }
        return false;
    }

    public function getExtraData(string $table , int $id) {
        $sql = "select * from $table where id_user = :id" ;
        $row = $this->db->getConnection()->prepare($sql);
        $row->bindParam(":id" , $id);
        $row->execute();
        return $row->fetch(PDO::FETCH_ASSOC);
    }
}

?>