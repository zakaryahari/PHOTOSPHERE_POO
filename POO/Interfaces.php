<?php 

interface UserRepositoryInterface {

    public function findById(int $id) : ?User ;
    
    public function findByEmail(string $email) : ?User ;

    public function save(User $user) : bool ;

    public function archive(int $id) : bool ;

    public function getAll() : array ;

}

?>