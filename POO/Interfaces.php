<?php 

interface UserRepositoryInterface {

    public function findById(int $id) : ?User ;
    
    public function findByEmail(string $email) : ?User ;

    public function save(User $user) : bool ;

    public function archive(int $id) : bool ;

    public function getAll() : array ;
    
}

interface PhotoRepositoryInterface {

    public function findById(int $id): ?Photo;

    public function getLatest(int $limit, int $offset): array;

    public function save(Photo $photo): bool;

    public function findByTag(string $tagName): array;

    public function search(string $query): array;

    public function archive(int $id): bool;
}

?>