<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Comment
{
    use Timestampable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 25,
     *      max = 2000,
     *      minMessage = "Un minimum de {{ limit }} caractètes est requis.",
     *      maxMessage = "Le maximum de {{ limit }} caractètes a été atteint."
     * )
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="Word", inversedBy="comments")
     */
    private $word;

    /**
     * @ORM\ManyToOne(targetEntity="Expression", inversedBy="comments")
     */
    private $expression;

    /**
     * @ORM\ManyToOne(targetEntity="Proverb", inversedBy="comments")
     */
    private $proverb;

    /**
     * @ORM\ManyToOne(targetEntity="Joke", inversedBy="comments")
     */
    private $joke;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getWord(): ?Word
    {
        return $this->word;
    }

    public function setWord(?Word $word): self
    {
        $this->word = $word;

        return $this;
    }

    public function getExpression(): ?Expression
    {
        return $this->expression;
    }

    public function setExpression(?Expression $expression): self
    {
        $this->expression = $expression;

        return $this;
    }

    public function getProverb(): ?Proverb
    {
        return $this->proverb;
    }

    public function setProverb(?Proverb $proverb): self
    {
        $this->proverb = $proverb;

        return $this;
    }

    public function getJoke(): ?Joke
    {
        return $this->joke;
    }

    public function setJoke(?Joke $joke): self
    {
        $this->joke = $joke;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Decides which model is being commented
     *
     * @param Word|Expression|Proverb|Joke $post
     */
    public function setPost($post)
    {
        switch (true) {
            case $post instanceof Word:
                $this->setWord($post);
                break;
            case $post instanceof Expression:
                $this->setExpression($post);
                break;
            case $post instanceof Proverb:
                $this->setProverb($post);
                break;
            case $post instanceof Joke:
                $this->setJoke($post);
                break;
            default:
                throw new \InvalidArgumentException('Erreur');
        }
    }
}
