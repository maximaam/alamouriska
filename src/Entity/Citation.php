<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CitationRepository")
 * @UniqueEntity(fields={"citation"}, message="msg.duplicate_post")
 * @ORM\HasLifecycleCallbacks
 */
class Citation extends AbstractEntity
{
    use Timestampable;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
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

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->getCitation();
    }
}
