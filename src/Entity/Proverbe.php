<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProverbeRepository")
 * @ORM\HasLifecycleCallbacks
 */
final class Proverbe extends AbstractEntity
{
    use Timestampable;

    /**
     * @ORM\Column(type="text")
     */
    private $proverbe;

    /**
     * @return string|null
     */
    public function getProverbe(): ?string
    {
        return $this->proverbe;
    }

    /**
     * @param string $proverbe
     * @return Proverbe
     */
    public function setProverbe(string $proverbe): self
    {
        $this->proverbe = $proverbe;

        return $this;
    }
}
