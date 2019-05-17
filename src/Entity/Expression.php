<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="expression", indexes={
 *     @ORM\Index(name="created_at_idx", columns={"created_at"}),
 *     @ORM\Index(name="status_idx", columns={"status"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\ExpressionRepository")
 * @UniqueEntity(fields={"expression"}, message="msg.duplicate_post")
 * @ORM\HasLifecycleCallbacks
 */
final class Expression extends AbstractPost
{
    use Timestampable;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank
     */
    private $expression;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="expressions")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @return string|null
     */
    public function getExpression(): ?string
    {
        return $this->expression;
    }

    /**
     * @param string $expression
     * @return Expression
     */
    public function setExpression(string $expression): self
    {
        $this->expression = $expression;

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
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
