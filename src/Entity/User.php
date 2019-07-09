<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 02.02.19
 * Time: 16:29
 */

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Criteria;
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
     * @ORM\OneToMany(targetEntity="Word", mappedBy="user", cascade="remove", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $words;

    /**
     * @ORM\OneToMany(targetEntity="Expression", mappedBy="user", cascade="remove", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $expressions;

    /**
     * @ORM\OneToMany(targetEntity="Proverb", mappedBy="user", cascade="remove", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $proverbs;

    /**
     * @ORM\OneToMany(targetEntity="Joke", mappedBy="user", cascade="remove", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $jokes;

    /**
     * @ORM\OneToMany(targetEntity="Blog", mappedBy="user", cascade="remove")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $blogs;

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
    private $allowPostNotification = true;

    /**
     * @ORM\OneToMany(targetEntity="Journal", mappedBy="user", orphanRemoval=true, cascade="remove")
     */
    private $journals;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user", orphanRemoval=true, cascade="remove")
     */
    private $comments;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $facebookId;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $remoteAddr;

    /**
     * User constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->createdAt = new \DateTime();

        $this->words = new ArrayCollection();
        $this->expressions = new ArrayCollection();
        $this->proverbs = new ArrayCollection();
        $this->jokes = new ArrayCollection();
        $this->journals = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
     * @return Collection|Word[]
     */
    public function getWords(): Collection
    {
        return $this->words;
    }

    /**
     * @return Collection|Expression[]
     */
    public function getExpressions(): Collection
    {
        return $this->expressions;
    }

    /**
     * @return Collection|Proverb[]
     */
    public function getProverbs(): Collection
    {
        return $this->proverbs;
    }

    /**
     * @return Collection|Joke[]
     */
    public function getJokes(): Collection
    {
        return $this->jokes;
    }

    /**
     * @return Collection|Blog[]
     */
    public function getBlogs(): Collection
    {
        return $this->blogs;
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

    /**
     * @return Collection|Journal[]
     */
    public function getJournals(): Collection
    {
        return $this->journals;
    }

    public function addJournal(Journal $journal): self
    {
        if (!$this->journals->contains($journal)) {
            $this->journals[] = $journal;
            $journal->setUser($this);
        }

        return $this;
    }

    public function removeJournal(Journal $journal): self
    {
        if ($this->journals->contains($journal)) {
            $this->journals->removeElement($journal);
            // set the owning side to null (unless already changed)
            if ($journal->getUser() === $this) {
                $journal->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return string|null
     */
    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    /**
     * @param string|null $facebookId
     * @return User
     */
    public function setFacebookId(?string $facebookId): self
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    public function getRemoteAddr(): ?string
    {
        return $this->remoteAddr;
    }

    public function setRemoteAddr(?string $remoteAddr): self
    {
        $this->remoteAddr = $remoteAddr;

        return $this;
    }

    /**
     * @param string $entity
     * @return Collection
     */
    /*
    public function activePosts(string $entity): Collection
    {
        return $this->{$entity}->matching(UserRepository::createActivePostsCriteria());
    }
    */
}
