<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 05.02.19
 * Time: 16:29
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Traits\Timestampable;

/**
 * @ORM\Table(name="mot",indexes={
 *     @ORM\Index(name="created_at_idx", columns={"created_at"}),
 *     @ORM\Index(name="in_latin_idx", columns={"in_latin"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\MotRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"inLatin"}, message="msg.duplicate_post")
 */
class Mot extends AbstractEntity
{
    use Timestampable;

    /**
     * @ORM\Column(type="string", length=128, unique=true)
     * @Assert\NotBlank
     * @Assert\Regex(pattern="/^[\w-]+$/", match=true, message="Un mot ne doit pas contenir d'espaces.")
     */
    private $inLatin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $inTamazight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $inArabic;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="mots")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @return string|null
     */
    public function getInLatin(): ?string
    {
        return $this->inLatin;
    }

    /**
     * @param string $inLatin
     * @return Mot
     */
    public function setInLatin(string $inLatin): self
    {
        $this->inLatin = $inLatin;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInTamazight(): ?string
    {
        return $this->inTamazight;
    }

    /**
     * @param string|null $inTamazight
     * @return Mot
     */
    public function setInTamazight(?string $inTamazight): self
    {
        $this->inTamazight = $inTamazight;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInArabic(): ?string
    {
        return $this->inArabic;
    }

    /**
     * @param string|null $inArabic
     * @return Mot
     */
    public function setInArabic(?string $inArabic): self
    {
        $this->inArabic = $inArabic;

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
        return $this->getInLatin();
    }
}
