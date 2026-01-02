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

?>