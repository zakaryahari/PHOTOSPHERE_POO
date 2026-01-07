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

    public function getLatest(int $limit): array;

    public function save(Photo $photo): bool;

    public function findByTag(string $tagName): array;

    public function search(string $query): array;

    public function archive(int $id): bool;
}

interface AlbumRepositoryInterface {

    public function findById(int $id) : ?Album;

    public function findPublic() : array;

    public function findUserAlbums(int $userId) : array;

    public function addPhotoToAlbum(int $id_photo, int $albumId, int $userId) : bool;

    public function removePhotoFromAlbum(int $id_photo, int $albumId, int $userId) : bool;

    public function save(Album $album) : bool ;
}

interface CommentRepositoryInterface {

    public function findById(int $id): ?Comment;

    public function findByPhoto(int $photoId): array;

    public function save(Comment $comment): bool;

    public function delete(int $id): bool;

    public function getReplies(int $parentId): array;
}

interface Taggable {

    public function addTag(string $tag): void ;

    public function removeTag(string $tag): void;

    public function getTags(): array ;

    public function hasTag(string $tag): bool;

    public function clearTags(): void;
}

interface Commentable {

    public function addComment(string $content, int $userId): int;

    public function removeComment(int $commentId): bool;

    public function getComments(): array;
    
    public function getCommentCount(): int;
}

interface Likeable {

    public function addLike(int $userId): bool;

    public function removeLike(int $userId): bool;

    public function isLikedBy(int $userId): bool;

    public function getLikeCount(): int;

    public function getLikedBy(): array;
}

?>