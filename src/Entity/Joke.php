<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="joke", indexes={
 *     @ORM\Index(name="created_at_idx", columns={"created_at"}),
 *     @ORM\Index(name="status_idx", columns={"status"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\JokeRepository")
 * @UniqueEntity(fields={"joke"}, message="msg.duplicate_post")
 * @ORM\HasLifecycleCallbacks
 */
class Joke extends AbstractPost
{
    use Timestampable;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private $joke;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="jokes")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @return string|null
     */
    public function getJoke(): ?string
    {
        return $this->joke;
    }

    /**
     * @param string $joke
     * @return Joke
     */
    public function setJoke(string $joke): self
    {
        $this->joke = $joke;

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
