<?php
/**
 * Created by PhpStorm.
 * User: hannah
 * Date: 16.02.19
 * Time: 22:33
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;
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
abstract class AbstractPost
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    const PAGINATOR_MAX = 3;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank
     */
    protected $status = self::STATUS_ACTIVE;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
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
     * @Vich\UploadableField(mapping="posts_image", fileNameProperty="imageName")
     */
    protected $imageFile;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $question = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addr;

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
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return AbstractPost
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

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
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return bool
     */
    public function getQuestion(): bool
    {
        return $this->question;
    }

    /**
     * @param bool $question
     * @return $this
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
     * @return $this
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddr(): ?string
    {
        return $this->addr;
    }

    /**
     * @param string|null $addr
     * @return $this
     */
    public function setAddr(?string $addr): self
    {
        $this->addr = $addr;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostMainEntry(): ?string
    {
        switch (true) {
            case $this instanceof Word:
                return $this->getInLatin();
                break;

            case $this instanceof Expression:
                return $this->getExpression();
                break;

            case $this instanceof Proverb:
                return $this->getProverb();
                break;

            case $this instanceof Joke:
                return $this->getJoke();
                break;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return static::class;
    }

}