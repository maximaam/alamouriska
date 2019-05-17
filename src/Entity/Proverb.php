<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="proverb", indexes={
 *     @ORM\Index(name="created_at_idx", columns={"created_at"}),
 *     @ORM\Index(name="status_idx", columns={"status"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\ProverbRepository")
 * @UniqueEntity(fields={"proverb"}, message="msg.duplicate_post")
 * @ORM\HasLifecycleCallbacks
 */
final class Proverb extends AbstractPost
{
    use Timestampable;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private $proverb;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="proverbs")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @return string|null
     */
    public function getProverb(): ?string
    {
        return $this->proverb;
    }

    /**
     * @param string $proverb
     * @return Proverb
     */
    public function setProverb(string $proverb): self
    {
        $this->proverb = $proverb;

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
