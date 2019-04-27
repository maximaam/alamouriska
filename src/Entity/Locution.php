<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocutionRepository")
 * @ORM\HasLifecycleCallbacks
 */
final class Locution extends AbstractEntity
{
    use Timestampable;

    /**
     * @ORM\Column(type="string", length=128)
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
}
