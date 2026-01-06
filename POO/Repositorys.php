<?php

require_once 'Interfaces.php';
require_once 'database.php';

class UserRepository implements UserRepositoryInterface {
    private Database $db ;

    public function __construct(Database $conn){
        $this->db = $conn;
    }


    public function findById(int $id) : ?User {
        $sql = "select * from User where id_user = :id";

        $query = $this->db->getConnection()->prepare($sql);
        $query->bindValue(":id" , $id,PDO::PARAM_INT);

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

    public function findByEmail(string $email) : ?User {
        $sql = "select * from User where email = :email";

        $query = $this->db->getConnection()->prepare($sql);
        $query->bindValue(":email" , $email);

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
        $row->bindValue(":id" , $id,PDO::PARAM_INT);
        $row->execute();
        return $row->fetch(PDO::FETCH_ASSOC);
    }

    public function save(User $user) : bool {
        if ($user->getId() == 0) {
            //insert
            $sql = "INSERT INTO User (username, email, password_hash, bio,status, profile_picture, role) VALUES (:username, :email, :password, :bio,:status, :pp, :role)";
            $query = $this->db->getConnection()->prepare($sql);

            $role = 'basic';
            if ($user instanceof Admin) $role = 'admin';
            if ($user instanceof ProUser) $role = 'pro';
            if ($user instanceof Moderator) $role = 'moderator';

            $query->bindValue(":username",$user->getUsername());
            $query->bindValue(":email",$user->getEmail());
            $query->bindValue(":password",$user->getPassword());
            $query->bindValue(":bio",$user->getBio());
            $query->bindValue(":status" , $user->getStatus());
            $query->bindValue(":pp",$user->getProfilePicture());
            $query->bindValue(":role",$role);

            $query->execute();
            $Id_Inserted = $this->db->getConnection()->lastInsertId(); 

            if ($user instanceof Admin) {
                $sql_role = "INSERT INTO Admin (id_user,is_super) values (:id,:is_super)";
                $query_role = $this->db->getConnection()->prepare($sql_role);
                $query_role->bindValue(":id",$Id_Inserted,PDO::PARAM_INT);
                $query_role->bindValue(":is_super",$user->getIsSuper());
                $query_role->execute();
            }
            if ($user instanceof BasicUser) {
                $sql_role = "INSERT INTO Basic_User (id_user,upload_count) values (:id,:upload_count)";
                $query_role = $this->db->getConnection()->prepare($sql_role);
                $query_role->bindValue(":id",$Id_Inserted,PDO::PARAM_INT);
                $query_role->bindValue(":upload_count",$user->getUploadCount());
                $query_role->execute();
            }
            if ($user instanceof ProUser) {
                $sql_role = "INSERT INTO Pro_User (id_user,subscription_start,subscription_end) values (:id,:subscription_start,:subscription_end)";
                $query_role = $this->db->getConnection()->prepare($sql_role);
                $query_role->bindValue(":id",$Id_Inserted,PDO::PARAM_INT);
                $query_role->bindValue(":subscription_start",$user->getSubscriptionStart()->format('Y-m-d'));
                $query_role->bindValue(":subscription_end",$user->getSubscriptionEnd()->format('Y-m-d'));
                $query_role->execute();
            }
            if ($user instanceof Moderator) {
                $sql_role = "INSERT INTO Moderator (id_user,level) values (:id,:level)";
                $query_role = $this->db->getConnection()->prepare($sql_role);
                $query_role->bindValue(":id",$Id_Inserted,PDO::PARAM_INT);
                $query_role->bindValue(":level",$user->getlevel());
                $query_role->execute();
            }
            return true;
        }
        if ($user->getId() > 0) {
            //update
            $sql_update = "UPDATE User set bio = :bio , profile_picture  = :pp ,status = :status ,password_hash = :password where id_user = :id";
            $query_update = $this->db->getConnection()->prepare($sql_update);
            $query_update->bindValue(":bio" , $user->getBio());
            $query_update->bindValue(":pp" , $user->getProfilePicture());
            $query_update->bindValue(":status" , $user->getStatus());
            $query_update->bindValue(":password" , $user->getPassword());
            $query_update->bindValue(":id" , $user->getId(),PDO::PARAM_INT);
            $query_update->execute();

            if ($user instanceof Admin) {
                $sql_role_update = "UPDATE Admin set is_super = :is_super where id_user = :id";
                $query_role_update = $this->db->getConnection()->prepare($sql_role_update);
                $query_role_update->bindValue(":is_user",$user->getIsSuper());
                $query_role_update->bindValue(":id",$user->getId(),PDO::PARAM_INT);
                $query_role_update->execute();
            }
            if ($user instanceof BasicUser) {
                $sql_role_update = "UPDATE Basic_User set upload_count = :upload_count where id_user = :id";
                $query_role_update = $this->db->getConnection()->prepare($sql_role_update);
                $query_role_update->bindValue(":upload_count",$user->getUploadCount(), PDO::PARAM_INT);
                $query_role_update->bindValue(":id",$user->getId(),PDO::PARAM_INT);
                $query_role_update->execute();
            }
            if ($user instanceof ProUser) {
                $sql_role_update = "UPDATE Pro_User set subscription_start = :subscription_start , subscription_end = :subscription_end where id_user = :id";
                $query_role_update = $this->db->getConnection()->prepare($sql_role_update);
                $query_role_update->bindValue(":subscription_start",$user->getSubscriptionStart()->format('Y-m-d'));
                $query_role_update->bindValue(":subscription_end",$user->getSubscriptionEnd()->format('Y-m-d'));
                $query_role_update->bindValue(":id",$user->getId(),PDO::PARAM_INT);
                $query_role_update->execute();
            }
            if ($user instanceof Moderator) {
                $sql_role_update = "UPDATE Moderator set level = :level where id_user = :id";
                $query_role_update = $this->db->getConnection()->prepare($sql_role_update);
                $query_role_update->bindValue(":level",$user->getlevel());
                $query_role_update->bindValue(":id",$user->getId(),PDO::PARAM_INT);
                $query_role_update->execute();
            }
            return true;
        }
        return false;
    }

    public function archive(int $id) : bool {
        $sql= "UPDATE User set status = 'archived' where id_user = :id";
        $query_archive = $this->db->getConnection()->prepare($sql);
        $query_archive->bindValue(":id",$id ,PDO::PARAM_INT);
        $query_archive->execute(); 
    }


    public function getAll() : array {
        $sql = "SELECT * FROM User";

        $query_allusers = $this->db->getConnection()->execute($sql);
        // $query_allusers->execute(); 
        $all_users = $query_allusers->fetchAll(PDO::FETCH_ASSOC);

        $users_array_objects = [];
        $userobject;

        foreach($all_users as $u){
            if ($u['role'] == 'admin') {
                $extra = $this->getExtraData('Admin', $u->getId);
                $users_array_objects[] = new Admin(array_merge($u, $extra));
            }
            if ($u['role'] == 'basic') {
                $extra = $this->getExtraData('Basic_User', $u->getId);
                $users_array_objects[] = new BasicUser(array_merge($u, $extra));
            }
            if ($u['role'] == 'pro') {
                $extra = $this->getExtraData('Pro_User', $u->getId);
                $users_array_objects[] = new ProUser(array_merge($u, $extra));
            }
            if ($u['role'] == 'moderator') {
                $extra = $this->getExtraData('Moderator', $u->getId);
                $users_array_objects[] = new Moderator(array_merge($u, $extra));
            }
        }
    }
}

class PhotoRepository implements PhotoRepositoryInterface {
    private Database $db ;

    public function __construct(Database $conn){
        $this->db = $conn;
    }

    public function findById(int $id): ?Photo {
        $sql = "SELECT * FROM Photo where id_photo = :id";

        $query = $this->db->getConnection()->prepare($sql);
        $query->bindValue(":id",$id);

        if($query->execute()) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new Photo($row);
            }
        }
        return null;
    }

    public function getLatest(int $limit): array{
        $sql_lastest = "SELECT * FROM Photo ORDER BY created_at DESC LIMIT :limit";

        $query_lastest = $this->db->getConnection()->prepare($sql_lastest);
        $query_lastest->bindValue(":limit",$limit, PDO::PARAM_INT);


        if($query_lastest->execute()) {
            $all_photos = $query_lastest->fetchAll(PDO::FETCH_ASSOC);
            if ($all_photos) {
                $array_allphotos = [];
                foreach($all_photos as $photo){
                    $array_allphotos[] = new Photo($photo);
                }
                return $array_allphotos;
            }
        }
        return null;
    }


    public function save(Photo $photo): bool {
        // INSERT
        if ($photo->getId() == 0) {
            $sql_insert = "INSERT INTO Photo (title, description, file_name, file_size, mime_type, dimensions, state, id_user) 
                        VALUES (:title, :description, :file_name, :file_size, :mime_type, :dimensions, :state, :id_user)";
            
            $query_insert = $this->db->getConnection()->prepare($sql_insert);
            
            $query_insert->bindValue(":title", $photo->getTitle());
            $query_insert->bindValue(":description", $photo->getDescription());
            $query_insert->bindValue(":file_name", $photo->getFileName());
            $query_insert->bindValue(":file_size", $photo->getFileSize(), PDO::PARAM_INT);
            $query_insert->bindValue(":mime_type", $photo->getMimeType());
            $query_insert->bindValue(":dimensions", $photo->getDimensions());
            $query_insert->bindValue(":state", $photo->getState());
            $query_insert->bindValue(":id_user", $photo->getUserId(), PDO::PARAM_INT);

            return $query_insert->execute();
        }

        // UPDATE
        if ($photo->getId() > 0) {
            $sql_update = "UPDATE Photo SET title = :title, description = :description, state = :state WHERE id_photo = :id";
            
            $query_update = $this->db->getConnection()->prepare($sql_update);
            
            $query_update->bindValue(":title", $photo->getTitle());
            $query_update->bindValue(":description", $photo->getDescription());
            $query_update->bindValue(":state", $photo->getState());
            $query_update->bindValue(":id", $photo->getId(), PDO::PARAM_INT);

            return $query_update->execute();
        }

        return false;
    }
    

    public function findByTag(string $tagName): array {
        $sql_filterbytag = "SELECT p.* FROM Photo p join Photo_Tags pt on p.id_photo = pt.id_photo join Tag t on pt.id_tag = t.id_tag where t.id_tag = :id_tag AND p.state = 'published' ORDER BY p.created_at DESC";

        $query_filterbytag = $this->db->getConnection()->prepare($sql);
        $query_filterbytag->bindValue(":tagName", $tagName);

        $photos = [];

        if ($query_filterbytag->execute()) {
            $rows = $query_filterbytag->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $photos[] = new Photo($row);
            }
        }

        return $photos;

    }

    public function search(string $query): array {

        $sql = "SELECT * FROM Photo WHERE (title LIKE :search OR description LIKE :search) AND state = 'published'";

        $query_search = $this->db->getConnection()->prepare($sql);
        

        $searchTerm = "%" . $query . "%";
        $query_search->bindValue(":search", $searchTerm);

        $results = [];

        if ($query_search->execute()) {
            $rows = $query_search->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $results[] = new Photo($row);
            }
        }

        return $results;
    }


    
}

?>