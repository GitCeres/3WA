<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FilmRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=FilmRepository::class)
 * @Vich\Uploadable
 * @UniqueEntity(
 *     fields={"title"},
 *     message="Le film {{ value }} existe déjà"
 * )
 */
class Film
{
    const ACTION = 'Action';
    const SF = 'Science Fiction';
    const FANTASTIQUE = 'Fantastique';
    const HORREUR = 'Horreur';
    const COMEDIE = 'Comédie';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $synopsis;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $director;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

     /**
     * @Vich\UploadableField(mapping="film_image", fileNameProperty="image")
     * @Assert\File(uploadIniSizeErrorMessage="L'image ne doit pas dépasser {{ limit }}{{ suffix }}")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="films")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Stars::class, mappedBy="film")
     */
    private $stars;
    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="film")
     */
    private $comment;

    private $average;

    public function __construct()
    {
        $this->stars = new ArrayCollection();
        $this->comment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getDirector(): ?string
    {
        return $this->director;
    }

    public function setDirector(string $director): self
    {
        $this->director = $director;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Stars>
     */
    public function getStars(): Collection
    {
        return $this->stars;
    }

    public function addStar(Stars $star): self
    {
        if (!$this->stars->contains($star)) {
            $this->stars[] = $star;
            $star->setFilm($this);
        }

        return $this;
    }

    public function removeStar(Stars $star): self
    {
        if ($this->stars->removeElement($star)) {
            // set the owning side to null (unless already changed)
            if ($star->getFilm() === $this) {
                $star->setFilm(null);
            }
        }

        return $this;
    }

    public function getAverage()
    {
        $stars = $this->stars;

        if ($stars->toArray() === []) {
            $this->average = null;
            return $this->average;
        }

        $total = 0;
        foreach ($stars as $star) {
            $total += $star->getNumber();
        }

        $this->average = round($total / count($stars), 1);

        return $this->average;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComment(): Collection
    {
        return $this->comment;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comment->contains($comment)) {
            $this->comment[] = $comment;
            $comment->setFilm($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comment->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getFilm() === $this) {
                $comment->setFilm(null);
            }
        }

        return $this;
    }
}
