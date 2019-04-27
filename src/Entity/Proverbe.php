<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProverbeRepository")
 * @ORM\HasLifecycleCallbacks
 */
final class Proverbe extends AbstractEntity
{
    use Timestampable;

    /**
     * @ORM\Column(type="text")
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
}
