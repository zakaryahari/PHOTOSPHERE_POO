<?php

abstract class User {
    protected int $id;
    protected string $username;
    protected string $email;
    protected string $password;
    protected DateTime $createdAt;
    protected DateTime $lastLogin;
    protected ?string $bio;
    protected ?string $profilePicture;

    public function __construct(array $data) {
        $this->id = $data['id_user'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->bio = $data['bio'] ?? null;
        $this->profilePicture = $data['profile_picture'] ?? null;
        $this->createdAt = new DateTime($data['created_at']) ?? date('Y-m-d H:i:s');
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

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getLastLogin(): DateTime {
        return $this->lastLogin;
    }


    public function verifyPassword($input): bool {
        return password_verify($input , $this->password);
    }

    public function updateLastLogin() {
        $this->lastLogin =  date('Y-m-d H:i:s');
    }

    abstract public function canCreatePrivateAlbum(): bool;
    abstract public function canUploadPhoto(): bool;
}

class BasicUser extends User {
    private int $uploadCount;

    public function __construct(array $data) {
        parent::__construct($data);
        $this->uploadCount = $data['upload_count'] ?? 0;
    }

    // Getters & Setters

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
        return $this->uploadCount < 10 ;
    }

    public function resetCounter(): void {
        $this->uploadCount = 0;
    }
}


class ProUser extends User {
    private DateTime $subscriptionStart;
    private DateTime $subscriptionEnd;

    public function __construct(array $data) {
        parent::__construct($data);
        $this->subscriptionStart = new DateTime($data['subscription_start']);
        $this->subscriptionEnd = clone $this->subscriptionStart;
        $this->subscriptionEnd->add(new DateInterval('P30D'));
    }

    // Getters & Setters

    public function getSubscriptionEnd() : DateTime {
        return $this->subscriptionEnd;
    }

    public function getSubscriptionStart() : DateTime {
        return $this->subscriptionStart;
    }
    
    public function setSubscriptionEnd(DateTime $date) : void {
        $this->subscriptionEnd = $date;
    }

    public function canCreatePrivateAlbum(): bool {
        return true ;
    }

    public function canUploadPhoto() : bool {
        return true ;
    }

    public function isSubscriptionActive() : bool {
        return date('Y-m-d H:i:s') < $this->subscriptionEnd;
    }
}

class Moderator extends User {
    protected string $level = 'senior';

    public function __construct(array $data) {
        parent::__construct($data);
        $this->level = $data['level'];
    }

    // Getters & Setters

    public function getlevel() : string {
        return $this->level;
    }

    public function setlevel(string $level) : void {
        $allowed = ['junior','senior', 'lead'];
        if (in_array($level , $allowed)) {
            $this->level = $level;
        }
    }
    
    public function canManageComment(): bool {
        if ($this->level === 'junior') {
            return false;
        }
        
        if ($this->level === 'senior' || $this->level === 'lead') {
            return true;
        }

        return false;
    }

    public function canSuspendUser(User $targetUser): bool {
        if ($targetUser instanceof Admin) {
            return false ;
        }

        if ($this->level == 'junior') {
            return false ;
        }
        return true;
    }

    public function canCreatePrivateAlbum(): bool {
        return true ;
    }

    public function canUploadPhoto() : bool {
        return true ;
    }
}


class Admin extends User {
    private bool $isSuper;

    public function __construct(array $data) {
        parent::__construct($data);
        $this->isSuper = $data['is_super'];
    }

    // Getters & Setters

    public function getIsSuper() : bool {
        return $this->isSuper;
    }

    public function setIsSuper(bool $status) : void {
        $this->isSuper = $status;
    }

    public function manageModerator(Moderator $mod, string $level) : void {
        $mod->setlevel($level);
    }

    public function canCreatePrivateAlbum(): bool {
        return true ;
    }

    public function canUploadPhoto() : bool {
        return true ;
    }
}

class Photo {
    protected int $id;
    protected string $title;
    protected ?string $description;
    protected string $fileName;
    protected int $fileSize;
    protected string $mimeType;
    protected string $dimensions;
    protected string $state;
    protected int $viewCount;
    protected int $userId;

    public function __construct(array $data) {
        $this->id = $data['id_photo'];
        $this->title = $data['title'];
        $this->description = $data['description'] ?? null;
        $this->fileName = $data['file_name'];
        $this->fileSize = $data['file_size'];
        $this->mimeType = $data['mime_type'];
        $this->dimensions = $data['dimensions'];
        $this->state = $data['state'] ?? 'draft';
        $this->viewCount = $data['view_count'] ?? 0;
        $this->userId = $data['id_user'];
    }

    // Getters & Setters
    public function getId(): int { 
        return $this->id; 
    }

    public function getTitle(): string { 
        return $this->title; 
    }

    public function getDescription(): ?string { 
        return $this->description; 
    }

    public function getFileName(): string { 
        return $this->fileName; 
    }

    public function getFileSize(): int { 
        return $this->fileSize; 
    }

    public function getMimeType(): string { 
        return $this->mimeType; 
    }

    public function getDimensions(): string { 
        return $this->dimensions; 
    }

    public function getState(): string { 
        return $this->state; 
    }

    public function getViewCount(): int { 
        return $this->viewCount; 
    }

    public function getUserId(): int { 
        return $this->userId; 
    }

    public function setTitle(string $title): void {}
    public function setState(string $state): void {}


    public function isPublished(): bool {}
}

class Album {
    protected int $id;
    protected string $name;
    protected ?string $description;
    protected bool $isPublic;
    protected int $userId;
    protected DateTime $createdAt;

    public function __construct(array $data) {
        $this->id = $data['id_album'];
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
        $this->isPublic = (bool)($data['is_public'] ?? true);
        $this->userId = $data['id_user'];
        $this->createdAt = new DateTime($data['created_at'] ?? 'now');
    }


    public function getId(): int { 
        return $this->id; 
    }

    public function getName(): string { 
        return $this->name; 
    }

    public function getDescription(): ?string { 
        return $this->description; 
    }

    public function getIsPublic(): bool { 
        return $this->isPublic; 
    }

    public function getUserId(): int { 
        return $this->userId; 
    }

    public function getCreatedAt(): DateTime { 
        return $this->createdAt; 
    }


    public function setName(string $name): void { }
    public function setIsPublic(bool $status): void { }


    public function canAccess(User $user): bool {}
}

class Comment {
    protected int $id;
    protected string $content;
    protected int $userId;
    protected int $photoId;
    protected ?int $parentId;
    protected DateTime $createdAt;

    public function __construct(array $data) {
        $this->id = $data['id_comment'];
        $this->content = $data['content'];
        $this->userId = $data['id_user'];
        $this->photoId = $data['id_photo'];
        $this->parentId = $data['parent_id'] ?? null;
        $this->createdAt = new DateTime($data['created_at'] ?? 'now');
    }

    public function getId(): int { 
        return $this->id; 
    }
    public function getContent(): string { 
        return $this->content; 
    }
    public function getUserId(): int { 
        return $this->userId; 
    }
    public function getPhotoId(): int { 
        return $this->photoId; 
    }
    public function getParentId(): ?int { 
        return $this->parentId; 
    }
    public function getCreatedAt(): DateTime { 
        return $this->createdAt; 
    }

    public function setContent(string $content): void { }

    public function isReply(): bool { }

    public function canBeEditedBy(User $user): bool {}
}

class Tag {
    protected int $id;
    protected string $name;

    public function __construct(array $data) {
        $this->id = $data['id_tag'];
        $this->name = $data['name'];
    }

    public function getId(): int { 
        return $this->id; 
    }
    public function getName(): string { 
        return $this->name; 
    }
    public function setName(string $name): void { }
}
?>