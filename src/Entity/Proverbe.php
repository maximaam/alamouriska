<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProverbeRepository")
 */
class Proverbe extends AbstractEntity
{
    /**
     * @ORM\Column(type="text")
     */
    private $proverbe;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

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

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Proverbe
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
