<?php

namespace App\Entity;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArticleRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;




#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[UniqueEntity('title')]

class Article
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    // Si bug enlever Integer //
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 10000, nullable: true)]
    private ?string $content = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;



    #[ORM\ManyToOne(inversedBy: 'article')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;


    /** construtor */
    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
       
       
    }

   
   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }
    

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

  
    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    public function __toString()
    {
        return $this->title;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }


 
}
