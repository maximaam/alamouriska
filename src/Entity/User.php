<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 02.02.19
 * Time: 16:29
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Mot", mappedBy="user")
     */
    private $mots;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->mots = new ArrayCollection();
    }

    /**
     * @return Collection|Mot[]
     */
    public function getMots(): Collection
    {
        return $this->mots;
    }

    public function addMot(Mot $mot): self
    {
        if (!$this->mots->contains($mot)) {
            $this->mots[] = $mot;
            $mot->setUser($this);
        }

        return $this;
    }

    public function removeMot(Mot $mot): self
    {
        if ($this->mots->contains($mot)) {
            $this->mots->removeElement($mot);
            // set the owning side to null (unless already changed)
            if ($mot->getUser() === $this) {
                $mot->setUser(null);
            }
        }

        return $this;
    }
}
