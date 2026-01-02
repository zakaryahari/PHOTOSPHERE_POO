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


class ProUser extends BasicUser {
    private string $subscriptionStart;
    private string $subscriptionEnd;

    public function __construct(string $username, string $email, string $password, string $start, string $end) {
        parent::__construct($username, $email, $password);
        $this->subscriptionStart = $start;
        $this->subscriptionEnd = $end;
    }

    
    public function canCreatePrivateAlbum(): bool {
        return true ;
    }
}

class Moderator extends User {
    protected string $level;

    public function __construct(string $username, string $email, string $password, string $level) {
        parent::__construct($username, $email, $password);
        $this->level = $level;
    }

    
    public function canCreatePrivateAlbum(): bool {
        return false ;
    }
}


?>