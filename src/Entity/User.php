<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 02.02.19
 * Time: 16:29
 */

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\{
    ArrayCollection, Collection
};

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 * @Vich\Uploadable
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
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=4,
     *     max=30,
     *     minMessage="fos_user.username.short",
     *     maxMessage="fos_user.username.long",
     *     groups={"Profile", "Registration"}
     * )
     */
    protected $username;

    /**
     * @Assert\Length(
     *     min=8,
     *     max=100,
     *     minMessage="fos_user.password.short",
     *     maxMessage="fos_user.password.long",
     *     groups={"Profile", "ResetPassword", "Registration", "ChangePassword"}
     * )
     * @Assert\Regex(
     *     pattern="/^[^\W_]+$/",
     *     message="user.password.difficulty",
     *     groups={"Profile", "ResetPassword", "Registration", "ChangePassword"}
     * )
     */
    protected $plainPassword;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $avatarName;

    /**
     * @var File
     * @Assert\Image(
     *     maxSize = "1M",
     *     mimeTypes = {"image/jpeg", "image/png"},
     *     minWidth = 128, maxWidth = 512,
     *     minHeight = 128, maxHeight = 512,
     *     minWidthMessage="Votre photo doit faire minimum 128px de largeur.",
     *     minHeightMessage="Votre photo doit faire minimum 128px de hauteur.",
     *     maxSizeMessage = "The maxmimum allowed file size is 1MB.",
     *     mimeTypesMessage = "Only the filetypes image are allowed."
     * )
     * @Vich\UploadableField(mapping="user_avatar", fileNameProperty="avatarName")
     */
    private $avatarFile;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Mot", mappedBy="user")
     */
    private $mots;

    /**
     * User constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->createdAt = new \DateTimeImmutable();
        $this->mots = new ArrayCollection();
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return User
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface|null $updatedAt
     * @return User
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    /**
     * @param string|null $avatarName
     * @return User
     */
    public function setAvatarName(?string $avatarName): self
    {
        $this->avatarName = $avatarName;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    /**
     * @param File|null $avatarFile
     * @return User
     * @throws \Exception
     */
    public function setAvatarFile(?File $avatarFile = null): self
    {
        $this->avatarFile = $avatarFile;

        if (null !== $avatarFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return Collection|Mot[]
     */
    public function getMots(): Collection
    {
        return $this->mots;
    }

    /**
     * @param Mot $mot
     * @return User
     */
    public function addMot(Mot $mot): self
    {
        if (!$this->mots->contains($mot)) {
            $this->mots[] = $mot;
            $mot->setUser($this);
        }

        return $this;
    }

    /**
     * @param Mot $mot
     * @return User
     */
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
