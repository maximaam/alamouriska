<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="joke", indexes={
 *     @ORM\Index(name="created_at_idx", columns={"created_at"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\JokeRepository")
 * @UniqueEntity(fields={"joke"}, message="msg.duplicate_post")
 * @ORM\HasLifecycleCallbacks()
 */
class Joke extends AbstractPost
{
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="jokes")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="joke", cascade="remove")
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return $this
     */
    public function setDescriptionEmpty(): self
    {
        $this->description = 'Ce champs est optionnel dans ce cas.';

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }
}
