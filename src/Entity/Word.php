<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 05.02.19
 * Time: 16:29
 */

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *     name="word",
 *     indexes={
 *      @ORM\Index(name="created_at_idx", columns={"created_at"}),
 *      @ORM\Index(name="post_idx", columns={"post"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\WordRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"inLatin"}, message="msg.duplicate_post")
 */
final class Word extends AbstractPost
{
    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank
     * @Assert\Regex(pattern="/^[\w-]+$/", match=true, message="Un mot ne doit pas contenir d'espaces.")
     */
    protected $post;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $inTamazight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $inArabic;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="words")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @return string|null
     */
    public function getInTamazight(): ?string
    {
        return $this->inTamazight;
    }

    /**
     * @param string|null $inTamazight
     * @return Word
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
     * @return Word
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
}
