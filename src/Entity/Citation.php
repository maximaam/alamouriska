<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CitationRepository")
 */
class Citation extends AbstractEntity
{
    use Timestampable;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $citation;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="citations")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @return string|null
     */
    public function getCitation(): ?string
    {
        return $this->citation;
    }

    /**
     * @param string $citation
     * @return Citation
     */
    public function setCitation(string $citation): self
    {
        $this->citation = $citation;

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
