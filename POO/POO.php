<?php

abstract class User {
    protected int $id;
    protected string $username;
    protected string $email;
    protected string $password;
    protected string $createdAt;
    protected string $lastLogin;
    protected string $bio;
    protected string $profilePicture;

    public function __construct(string $username, string $email, string $password) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    public function login(): bool {}

    abstract public function canCreatePrivateAlbum(): bool;
}

class BasicUser extends User {
    protected int $uploadCount;

    public function __construct(string $username, string $email, string $password, int $uploadCount = 0) {
        parent::__construct($username, $email, $password);
        $this->uploadCount = $uploadCount;
    }

    public function resetCounter(): bool {}

    public function canCreatePrivateAlbum(): bool {
        return false ;
    }

}
?>