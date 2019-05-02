<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RatingRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Rating
{
    use Timestampable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $addr;

    /**
     * @ORM\Column(type="smallint")
     */
    private $rating;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddr(): ?string
    {
        return $this->addr;
    }

    public function setAddr(string $addr): self
    {
        $this->addr = $addr;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
