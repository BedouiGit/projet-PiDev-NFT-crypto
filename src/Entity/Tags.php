<?php

namespace App\Entity;

use App\Repository\TagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagsRepository::class)]
class Tags
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Article::class, inversedBy: 'tags')]
    private Collection $relation;

    #[ORM\Column(length: 255)]
    private ?string $phototag = null;

    public function __construct()
    {
        $this->relation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getRelation(): Collection
    {
        return $this->relation;
    }

    public function addRelation(Article $relation): static
    {
        if (!$this->relation->contains($relation)) {
            $this->relation->add($relation);
        }

        return $this;
    }

    public function removeRelation(Article $relation): static
    {
        $this->relation->removeElement($relation);

        return $this;
    }

    public function __toString(){
        return $this->nom?: '(No title)';
    }

    public function getPhototag(): ?string
    {
        return $this->phototag;
    }

    public function setPhototag(string $phototag): static
    {
        $this->phototag = $phototag;

        return $this;
    }
}
