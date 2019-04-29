<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocutionRepository")
 * @UniqueEntity(fields={"locution"}, message="msg.duplicate_post")
 * @ORM\HasLifecycleCallbacks
 */
final class Locution extends AbstractEntity
{
    use Timestampable;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank
     */
    private $locution;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="locutions")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @return string|null
     */
    public function getLocution(): ?string
    {
        return $this->locution;
    }

    /**
     * @param string $locution
     * @return Locution
     */
    public function setLocution(string $locution): self
    {
        $this->locution = $locution;

        return $this;
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
     * @return string|null
     */
    public function __toString()
    {
        return $this->getLocution();
    }
}
