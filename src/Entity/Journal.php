<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JournalRepository")
 * @ORM\HasLifecycleCallbacks
 */
final class Journal
{
    use Timestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addr;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="journals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getAddr(): ?string
    {
        return $this->addr;
    }

    /**
     * @param string|null $addr
     * @return $this
     */
    public function setAddr(?string $addr): self
    {
        $this->addr = $addr;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Journal
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->User;
    }

    /**
     * @param User|null $User
     * @return Journal
     */
    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }
}
