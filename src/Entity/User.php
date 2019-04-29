<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 02.02.19
 * Time: 16:29
 */

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user", indexes={@ORM\Index(name="username_idx", columns={"username"})})
 * @Vich\Uploadable
 */
class User extends BaseUser
{
    use Timestampable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^[[:alnum:]]+$/")
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
     *     minWidth = 128, maxWidth = 800,
     *     minHeight = 128, maxHeight = 800,
     *     minWidthMessage="Votre photo doit faire minimum 128px de largeur.",
     *     minHeightMessage="Votre photo doit faire minimum 128px de hauteur.",
     *     maxSizeMessage = "The maxmimum allowed file size is 1MB.",
     *     mimeTypesMessage = "Only the filetypes image are allowed."
     * )
     * @Vich\UploadableField(mapping="user_avatar", fileNameProperty="avatarName")
     */
    private $avatarFile;

    /**
     * @ORM\OneToMany(targetEntity="Mot", mappedBy="user")
     */
    private $mots;

    /**
     * @ORM\OneToMany(targetEntity="Locution", mappedBy="user")
     */
    private $locutions;

    /**
     * @ORM\OneToMany(targetEntity="Proverbe", mappedBy="user")
     */
    private $proverbes;

    /**
     * @ORM\OneToMany(targetEntity="Citation", mappedBy="user")
     */
    private $citations;

    /**
     * Allow member to write to another member
     *
     * @ORM\Column(type="boolean")
     */
    private $allowMemberContact = true;

    /**
     * Member gets a notification on every new post
     *
     * @ORM\Column(type="boolean")
     */
    private $allowPostNotification = false;

    /**
     * User constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->mots = new ArrayCollection();
        $this->locutions = new ArrayCollection();
        $this->proverbes = new ArrayCollection();
        $this->citations = new ArrayCollection();
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
     * @return Collection|Locution[]
     */
    public function getLocutions(): Collection
    {
        return $this->locutions;
    }

    /**
     * @return Collection|Proverbe[]
     */
    public function getProverbes(): Collection
    {
        return $this->proverbes;
    }

    /**
     * @return Collection|Citation[]
     */
    public function getCitations(): Collection
    {
        return $this->citations;
    }

    /**
     * @return bool|null
     */
    public function getAllowMemberContact(): ?bool
    {
        return $this->allowMemberContact;
    }

    /**
     * @param bool $val
     * @return User
     */
    public function setAllowMemberContact(bool $val): self
    {
        $this->allowMemberContact = $val;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAllowPostNotification(): ?bool
    {
        return $this->allowPostNotification;
    }

    /**
     * @param bool $val
     * @return User
     */
    public function setAllowPostNotification(bool $val): self
    {
        $this->allowPostNotification = $val;

        return $this;
    }
}
