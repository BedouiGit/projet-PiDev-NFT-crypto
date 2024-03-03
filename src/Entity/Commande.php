<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $Wallet = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\OneToMany(targetEntity: NFT::class, mappedBy: 'commande')]
    private Collection $nfts;

    public function __construct()
    {
        $this->nfts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getWallet(): ?string
    {
        return $this->Wallet;
    }

    public function setWallet(string $Wallet): static
    {
        $this->Wallet = $Wallet;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection<int, nft>
     */
    public function getNfts(): Collection
    {
        return $this->nfts;
    }

    public function addNft(nft $nft): static
    {
        if (!$this->nfts->contains($nft)) {
            $this->nfts->add($nft);
            $nft->setCommande($this);
        }

        return $this;
    }

    public function removeNft(nft $nft): static
    {
        if ($this->nfts->removeElement($nft)) {
            // set the owning side to null (unless already changed)
            if ($nft->getCommande() === $this) {
                $nft->setCommande(null);
            }
        }

        return $this;
    }

    
    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload): void
    {
        if (strpos($this->Wallet, ' ') !== false) {
            $context->buildViolation('The wallet address cannot contain spaces.')
                    ->atPath('wallet')
                    ->addViolation();
        }
    }

    
}
