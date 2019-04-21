<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 05.02.19
 * Time: 16:29
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * When a user deleted a 'mot', keep a copy here
 *
 * @ORM\Entity(repositoryClass="App\Repository\MotDeletedRepository")
 */
final class MotDeleted
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="string", length=128)
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
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $userId;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param \DateTimeInterface $date
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $date): self
    {
        $this->createdAt = $date;

        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getInLatin(): ?string
    {
        return $this->inLatin;
    }

    /**
     * @param string $inLatin
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setInArabic(?string $inArabic): self
    {
        $this->inArabic = $inArabic;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return $this
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }
}
