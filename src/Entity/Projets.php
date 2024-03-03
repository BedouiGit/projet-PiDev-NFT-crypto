<?php

namespace App\Entity;

use App\Repository\ProjetsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjetsRepository::class)]
class Projets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $Description = null;

    #[ORM\Column(length: 255)]
    private ?string $WalletAddress = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateDeCreation = null;

    #[ORM\Column(length: 255)]
    private ?string $photoURL = null;

    #[ORM\OneToMany(targetEntity: NFT::class, mappedBy: 'projets')]
    private Collection $nft;

    #[ORM\ManyToOne(inversedBy: 'Projets')]
    private ?Category $category ;
    
    public function __construct()
    {
        $this->nft = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }
    
    public function setCategory(Category $category): static
    {
        $this->category = $category;
    
        return $this;
    }
    
    public function getWalletAddress(): ?string
    {
        return $this->WalletAddress;
    }

    public function setWalletAddress(string $WalletAddress): static
    {
        $this->WalletAddress = $WalletAddress;

        return $this;
    }

    public function getDateDeCreation(): ?\DateTimeInterface
    {
        return $this->DateDeCreation;
    }

    public function setDateDeCreation(\DateTimeInterface $DateDeCreation): static
    {
        $this->DateDeCreation = $DateDeCreation;

        return $this;
    }

    public function getPhotoURL(): ?string
    {
        return $this->photoURL;
    }

    public function setPhotoURL(string $photoURL): static
    {
        $this->photoURL = $photoURL;

        return $this;
    }

    /**
     * @return Collection<int, NFT>
     */
    public function getNft(): Collection
    {
        return $this->nft;
    }

    public function addNft(NFT $nft): static
    {
        if (!$this->nft->contains($nft)) {
            $this->nft->add($nft);
            $nft->setProjets($this);
        }

        return $this;
    }

    public function removeNft(NFT $nft): static
    {
        if ($this->nft->removeElement($nft)) {
            // set the owning side to null (unless already changed)
            if ($nft->getProjets() === $this) {
                $nft->setProjets(null);
            }
        }

        return $this;
    }
}
