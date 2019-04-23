<?php
/**
 * Created by PhpStorm.
 * User: hannah
 * Date: 16.02.19
 * Time: 22:33
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class EntityBase
 *
 * Super abstract class to define shared mapping information between entities
 *
 * @ORM\MappedSuperclass
 * @Vich\Uploadable
 *
 * @package App\Entity
 */
abstract class AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $imageName;

    /**
     * @var File
     * @Assert\Image(
     *     maxSize = "1M",
     *     mimeTypes = {"image/jpeg"},
     *     mimeTypesMessage = "msg.jpeg_only"
     * )
     * @Vich\UploadableField(mapping="mot_image", fileNameProperty="imageName")
     */
    protected $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="mots")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $question = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $slug;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return string
     */
    public function __toString()
    {
        return static::class;
    }

}