<?php

require_once 'Interfaces.php';
require_once 'database.php';

class UserRepository implements UserRepositoryInterface {
    private Database $db ;

    public function __construct(Database $conn){
        $this->db = $conn;
    }

}

?>