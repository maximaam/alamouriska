<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 05.02.19
 * Time: 16:29
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="mot",indexes={@ORM\Index(name="created_at_idx", columns={"created_at"})})
 * @ORM\Entity(repositoryClass="App\Repository\MotRepository")
 * @UniqueEntity(fields={"inLatin"}, message="msg.duplicate_mot")
 * @Vich\Uploadable
 */
class Mot extends AbstractEntity
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status = self::STATUS_ACTIVE;

    /**
     * @ORM\Column(type="string", length=128, unique=true)
     */
    private $inLatin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $inTamazight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $inArabic;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $imageName;

    /**
     * @var File
     * @Assert\Image(
     *     maxSize = "1M",
     *     mimeTypes = {"image/jpeg"},
     *     mimeTypesMessage = "msg.jpeg_only"
     * )
     * @Vich\UploadableField(mapping="mot_image", fileNameProperty="imageName")
     */
    private $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="mots")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $question = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;



    /**
     * @return string|null
     */
    public function getInLatin(): ?string
    {
        return $this->inLatin;
    }

    /**
     * @param string $inLatin
     * @return Mot
     */
    public function setInLatin(string $inLatin): self
    {
        $this->inLatin = $inLatin;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInTamazight(): ?string
    {
        return $this->inTamazight;
    }

    /**
     * @param string|null $inTamazight
     * @return Mot
     */
    public function setInTamazight(?string $inTamazight): self
    {
        $this->inTamazight = $inTamazight;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInArabic(): ?string
    {
        return $this->inArabic;
    }

    /**
     * @param string|null $inArabic
     * @return Mot
     */
    public function setInArabic(?string $inArabic): self
    {
        $this->inArabic = $inArabic;

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
     * @param string $description
     * @return Mot
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * @param string|null $imageName
     * @return Mot
     */
    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     * @return Mot
     * @throws \Exception
     */
    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return Mot
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return bool
     */
    public function getQuestion(): bool
    {
        return $this->question;
    }

    /**
     * @param bool $question
     * @return Mot
     */
    public function setQuestion(bool $question): self
    {
        $this->question = $question;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return Mot
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Mot
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
