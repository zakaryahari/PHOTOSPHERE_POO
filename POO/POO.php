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

    public function __construct(array $data) {
        $this->id = $data['id_user'] ?? 0;
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->bio = $data['bio'] ?? null;
        $this->profilePicture = $data['profile_picture'] ?? null;
        $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    // public function login(): bool {}

    // Getters & Setters

    public function getId(): int {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername(string $username): void {
        if (strlen($username) >= 3 && strlen($username) <= 50) {
            $this->username = $username;
        }
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function getBio(): ?string {
        return $this->bio;
    }

    public function setBio(string $bio): void {
        if (strlen($bio) <= 1000) {
            $this->bio = $bio;
        }
    }

    public function getProfilePicture(): ?string {
        return $this->profilePicture;
    }

    public function setProfilePicture(string $path): void {
        $this->profilePicture = $path;
    }

    public function getCreatedAt(): string {
        return $this->createdAt;
    }

    public function getLastLogin(): string {
        return $this->lastLogin;
    }


    public function verifyPassword($input): bool {
        return password_verify($input , $this->password);
    }

    public function updateLastLogin() {
        $this->lastLogin =  date('Y-m-d H:i:s');
    }

    abstract public function canCreatePrivateAlbum(): bool;
}

class BasicUser extends User {
    private int $uploadCount;

    public function __construct(string $username, string $email, string $password, int $uploadCount = 0) {
        parent::__construct($username, $email, $password);
        $this->uploadCount = $uploadCount;
    }

    public function getUploadCount(): int {
        return $this->uploadCount;
    }

    public function incrementUploadCount(): void {
        $this->uploadCount++;
    }

    public function canCreatePrivateAlbum(): bool {
        return false ;
    }

    public function canUploadPhoto() : bool {
        return $this->uploadCount < 11 ;
    }

    public function resetCounter(): void {
        $this->uploadCount = 0;
    }
}


class ProUser extends User {
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
        return true ;
    }
}


class Admin extends User {
    private bool $isSuper;

    public function __construct(string $username, string $email, string $password, bool $isSuper = false) {
        parent::__construct($username, $email, $password);
        $this->isSuper = $isSuper;
    }

    public function canCreatePrivateAlbum(): bool {
        return true ;
    }
}

?>