<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Thread as BaseThread;
use phpDocumentor\Reflection\Types\This;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ThreadRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Thread extends BaseThread
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $postId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $post;

    /**
     * @ORM\Column(type="text")
     */
    private $postMainEntry;

    /**
     * @param int $by
     * @return int
     */
    public function decrementNumComments($by = 1)
    {
        return $this->numComments -= intval($by);
    }

    public function getPostId(): ?int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): self
    {
        $this->postId = $postId;

        return $this;
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

    public function getPostMainEntry(): ?string
    {
        return $this->postMainEntry;
    }

    public function setPostMainEntry(string $postMainEntry): self
    {
        $this->postMainEntry = $postMainEntry;

        return $this;
    }


}
