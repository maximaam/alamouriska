<?php
/**
 * Created by PhpStorm.
 * User: hannah
 * Date: 16.02.19
 * Time: 22:33
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EntityBase
 *
 * Super abstract class to define shared mapping information between entities
 *
 * @ORM\MappedSuperclass
 *
 * @package App\Entity
 */
abstract class AbstractEntity
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * AbstractEntity constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

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
     * @param \DateTimeInterface|null $date
     * @return $this
     */
    public function setUpdatedAt(?\DateTimeInterface $date): self
    {
        $this->updatedAt = $date;

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
     * @return \DateTimeInterface
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

}