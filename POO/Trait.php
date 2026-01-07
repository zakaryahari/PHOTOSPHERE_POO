<?php

trait TaggableTrait {
    protected array $tags = [];

    private function normalizeTag(string $tag): string {
        return strtolower(trim($tag));
    }

    public function addTag(string $tag): void {
        $normalized = $this->normalizeTag($tag);
        if (!$this->hasTag($normalized)) {
            $this->tags[] = $normalized;
        }
    }

    public function removeTag(string $tag): void {
        $normalized = $this->normalizeTag($tag);
        $this->tags = array_values(array_diff($this->tags, [$normalized]));
    }

    public function hasTag(string $tag): bool {
        $normalized = $this->normalizeTag($tag);
        return in_array($normalized, $this->tags);
    }

    public function getTags(): array {
        return $this->tags;
    }

    public function clearTags(): void {
        $this->tags = [];
    }
}

trait TimestampableTrait {
    protected DateTimeInterface $createdAt;
    protected ?DateTimeInterface $updatedAt = null;

    public function initializeTimestamps(): void {
        $this->createdAt = new DateTime();
    }

    public function updateTimestamps(): void {
        $this->updatedAt = new DateTime();
    }

    public function getCreatedAt(): DateTimeInterface {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface {
        return $this->updatedAt;
    }
}

trait LikeableTrait {
    protected int $likeCount = 0;
    protected array $likedBy = [];

    public function addLike(int $userId): bool {
        if (!$this->isLikedBy($userId)) {
            $this->likedBy[] = $userId;
            return true;
        }
        return false;
    }

    public function removeLike(int $userId): bool {
        $this->likedBy = array_values(array_diff($this->likedBy, [$userId]));
        return true;
    }

    public function isLikedBy(int $userId): bool {
        if (in_array($userId, $this->likedBy)) {
            return true;
        }
        return false;
    }

    public function getLikeCount(): int {
        return $this->likeCount++;
    }

    public function getLikedBy(): array {
        return $this->likedBy;
    }
}

?>