<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocutionRepository")
 */
class Locution extends AbstractEntity
{
    /**
     * @ORM\Column(type="text")
     */
    private $locution;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @return string|null
     */
    public function getLocution(): ?string
    {
        return $this->locution;
    }

    /**
     * @param string $locution
     * @return Locution
     */
    public function setLocution(string $locution): self
    {
        $this->locution = $locution;

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
     * @param string|null $description
     * @return Locution
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
