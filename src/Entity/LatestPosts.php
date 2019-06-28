<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LatestPostsRepository")
 */
class LatestPosts
{
    use Timestampable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $post;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $permalink;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $username;

    /**
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasAvatar;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?string
    {
        return $this->post;
    }

    public function setPost(string $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getPermalink(): ?string
    {
        return $this->permalink;
    }

    public function setPermalink(string $permalink): self
    {
        $this->permalink = $permalink;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getHasAvatar(): ?bool
    {
        return $this->hasAvatar;
    }

    public function setHasAvatar(bool $hasAvatar): self
    {
        $this->hasAvatar = $hasAvatar;

        return $this;
    }
}
