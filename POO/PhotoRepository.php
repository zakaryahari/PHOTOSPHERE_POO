<?php 

require_once 'database.php';
require_once 'POO.php';

class PhotoRepository {

    private Database $PDO;

    
    public function __construct(){
        $this->PDO = Database::getInstance();
    }

    public function saveWithTags(array $arrayTags , Photo $photo) : bool {
        $size = count($arrayTags);

        try {
            if ($size > 1 && $size <=10) {
                $this->PDO->getConnection()->beginTransaction();
                    foreach ($arrayTags as $tags) {

                            $sql = "Select * from Tag where name = :name ";
                            $query = $this->PDO->getConnection()->prepare($sql);
                            $query->bindValue(":name" , $tags);
                            $query->execute();
                            $result = $query->fetchAll(PDO::FETCH_ASSOC);
                            if (count($result) == 0) {
                                $sql_insert_tag = "INSERT INTO Tag(name) values (:name)";
                                $query_insert_tag = $this->PDO->getConnection()->prepare($sql_insert_tag);
                                $query_insert_tag->bindValue(":name" , $tags);
                                $query_insert_tag->execute();
                                $Id_Inserted = $this->PDO->getConnection()->lastInsertId();
                                
                                $sql_insert_Photo_Tags = "INSERT INTO Photo_Tags (id_photo , id_tag) values (:id_photo,:id_tag)";
                                $query_insert_Photo_Tags = $this->PDO->getConnection()->prepare($sql_insert_Photo_Tags);
                                $query_insert_Photo_Tags->bindValue(":id_photo" , $photo->getId(),PDO::PARAM_INT);
                                $query_insert_Photo_Tags->bindValue(":id_tag",$Id_Inserted,PDO::PARAM_INT);
                                
                                $query_insert_Photo_Tags->execute();   
                                $this->PDO->getConnection()->commit();
                            }
                    }
                    $this->PDO->getConnection()->commit();
                    return true;          
                }
            
            else {
                echo "minimum 1 tag, maximum 10 tags";
            }
        } catch (Throwable $e) {
            $this->PDO->getConnection()->rollback();
        }
        return false;
    }
}


$repo_photo = new PhotoRepository();

$tags_array = ["php", "code", "programming", "web development"];

$data = [
    'id_photo' => 3,
    'title' => 'qjksdh',
    'file_name' => 'qjsdhk',
    'file_size' => 13214,
    'mime_type' => 'qhbsdjk',
    'dimensions' => 'hjbqhbf',
    'id_user' => 3
];

$photo = new Photo($data);

print_r($photo);

$repo_photo->saveWithTags($tags_array , $photo);

?>