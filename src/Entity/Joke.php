<?php

namespace App\Entity;

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
final class Joke extends AbstractPost
{
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="jokes")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

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
}
