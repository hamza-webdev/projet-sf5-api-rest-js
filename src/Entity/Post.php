<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Comment;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private string $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     */
    private User $author;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="post")
     */
    private Comment $comments;

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="post_likes")
     */
    private array $likedBy;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $publishedAt;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public static function create(string $content, string $title, User $author)
    {
        $post = new self();
        $post->content = $content;
        $post->title = $title;
        $post->author = $author;

        return $post;

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * 
     *
     * @return User[]|null
     */
    public function getLikedBy(): ?array
    {
        return $this->likedBy;
    }

    /**
     * 
     * @param   User[]  $likedBy  [$likedBy description]
     *
     * @return  self             [return description]
     */
    public function setLikedBy(?array $likedBy): self
    {
        $this->likedBy = $likedBy;

        return $this;
    }

    public function likeBy(User $user): void
    {
        $this->likedBy[] = $user;
    }

    public function disLikeBy(User $user): void
    {
        
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    



}
