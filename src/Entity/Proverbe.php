<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProverbeRepository")
 * @UniqueEntity(fields={"proverbe"}, message="msg.duplicate_post")
 * @ORM\HasLifecycleCallbacks
 */
final class Proverbe extends AbstractEntity
{
    use Timestampable;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private $proverbe;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="proverbes")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @return string|null
     */
    public function getProverbe(): ?string
    {
        return $this->proverbe;
    }

    /**
     * @param string $proverbe
     * @return Proverbe
     */
    public function setProverbe(string $proverbe): self
    {
        $this->proverbe = $proverbe;

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
        return $this->getProverbe();
    }
}
