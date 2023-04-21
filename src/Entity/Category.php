<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['produit:list'])]
    private ?string $typePet = null;

    #[ORM\ManyToMany(targetEntity: Produit::class, inversedBy: 'categories')]
    private Collection $category;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $moditfyAt = null;

    public function __construct()
    {
        $this->category = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypePet(): ?string
    {
        return $this->typePet;
    }

    public function setTypePet(string $typePet): self
    {
        $this->typePet = $typePet;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Produit $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(Produit $category): self
    {
        $this->category->removeElement($category);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModitfyAt(): ?\DateTimeInterface
    {
        return $this->moditfyAt;
    }

    public function setModitfyAt(?\DateTimeInterface $moditfyAt): self
    {
        $this->moditfyAt = $moditfyAt;

        return $this;
    }
}
