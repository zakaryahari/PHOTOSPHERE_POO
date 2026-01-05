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

    public function save(User $user) : bool {
        if ($user->getId() == 0) {
            //insert
            $sql = "INSERT INTO User (username, email, password_hash, bio, profile_picture, role) VALUES (:username, :email, :password, :bio, :pp, :role)";
            $query = $this->db->getConnection()->prepare($sql);

            $role = 'basic';
            if ($user instanceof Admin) $role = 'admin';
            if ($user instanceof ProUser) $role = 'pro';
            if ($user instanceof Moderator) $role = 'moderator';

            $query->bindParam(":username",$user->getUsername());
            $query->bindParam(":email",$user->getEmail());
            $query->bindParam(":password",$user->getPassword());
            $query->bindParam(":bio",$user->getBio());
            $query->bindParam(":pp",$user->getProfilePicture());
            $query->bindParam(":role",$role);

            $query->execute();
            $Id_Inserted = $this->db->getConnection()->lastInsertId(); 

            if ($user instanceof Admin) {
                $sql_role = "INSERT INTO Admin (id_user,is_super) values (:id,:is_super)";
                $query_role = $this->db->getConnection()->prepare($sql_role);
                $query_role->bindParam(":id",$Id_Inserted);
                $query_role->bindParam(":is_super",$user->getIsSuper());
                $query_role->execute();
            }
            if ($user instanceof BasicUser) {
                $sql_role = "INSERT INTO Basic_User (id_user,upload_count) values (:id,:upload_count)";
                $query_role = $this->db->getConnection()->prepare($sql_role);
                $query_role->bindParam(":id",$Id_Inserted);
                $query_role->bindParam(":upload_count",$user->getUploadCount());
                $query_role->execute();
            }
            if ($user instanceof ProUser) {
                $sql_role = "INSERT INTO Pro_User (id_user,subscription_start,subscription_end) values (:id,:subscription_start,:subscription_end)";
                $query_role = $this->db->getConnection()->prepare($sql_role);
                $query_role->bindParam(":id",$Id_Inserted);
                $query_role->bindParam(":subscription_start",$user->getSubscriptionStart()->format('Y-m-d'));
                $query_role->bindParam(":subscription_end",$user->getSubscriptionEnd()->format('Y-m-d'));
                $query_role->execute();
            }
            if ($user instanceof Moderator) {
                $sql_role = "INSERT INTO Moderator (id_user,level) values (:id,:level)";
                $query_role = $this->db->getConnection()->prepare($sql_role);
                $query_role->bindParam(":id",$Id_Inserted);
                $query_role->bindParam(":level",$user->getlevel());
                $query_role->execute();
            }
            
        }
        if ($user->getId() > 0) {
            //update
            $sql_update = "UPDATE User set bio = :bio , profile_picture = :pp ,password_hash = :password where id_user = :id";
            $query_update = $this->db->getConnection()->prepare($sql_update);
            $query_update->bindParam(":bio" , $user->getBio());
            $query_update->bindParam(":pp" , $user->getProfilePicture());
            $query_update->bindParam(":password" , $user->getPassword());
            $query_update->bindParam(":id" , $user->getId());
            $query_update->execute();

            if ($user instanceof Admin) {
                $sql_role_update = "UPDATE Admin set is_super = :is_super where id_user = :id";
                $query_role_update = $this->db->getConnection()->prepare($sql_role_update);
                $query_role_update->bindParam(":is_user",$user->getIsSuper());
                $query_role_update->bindParam(":id",$user->getId());
                $query_role_update->execute();
            }
            if ($user instanceof BasicUser) {
                $sql_role_update = "UPDATE Basic_User set upload_count = :upload_count where id_user = :id";
                $query_role_update = $this->db->getConnection()->prepare($sql_role_update);
                $query_role_update->bindParam(":upload_count",$user->getUploadCount());
                $query_role_update->bindParam(":id",$user->getId());
                $query_role_update->execute();
            }
            if ($user instanceof ProUser) {
                $sql_role_update = "UPDATE Pro_User set subscription_start = :subscription_start , subscription_end = :subscription_end where id_user = :id";
                $query_role_update = $this->db->getConnection()->prepare($sql_role_update);
                $query_role_update->bindParam(":subscription_start",$user->getSubscriptionStart()->format('Y-m-d'));
                $query_role_update->bindParam(":subscription_end",$user->getSubscriptionEnd()->format('Y-m-d'));
                $query_role_update->bindParam(":id",$user->getId());
                $query_role_update->execute();
            }
            if ($user instanceof Moderator) {
                $sql_role_update = "UPDATE Moderator set level = :level where id_user = :id";
                $query_role_update = $this->db->getConnection()->prepare($sql_role_update);
                $query_role_update->bindParam(":level",$user->getIsSuper());
                $query_role_update->bindParam(":id",$user->getlevel());
                $query_role_update->execute();
            }
        }
    }
}

?>