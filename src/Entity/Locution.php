<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocutionRepository")
 * @ORM\HasLifecycleCallbacks
 */
final class Locution extends AbstractEntity
{
    use Timestampable;

    /**
     * @ORM\Column(type="text")
     */
    private $locution;

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
}
