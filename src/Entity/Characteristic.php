<?php

namespace App\Entity;

use App\Repository\CharacteristicRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CharacteristicRepository::class)]
class Characteristic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['produit:list'])]
    private ?int $cendres = null;

    #[ORM\Column]
    #[Groups(['produit:list'])]
    private ?int $eau = null;

    #[ORM\Column]
    #[Groups(['produit:list'])]
    private ?int $fibre = null;

    #[ORM\Column]
    #[Groups(['produit:list'])]
    private ?int $glucide = null;

    #[ORM\Column]
    #[Groups(['produit:list'])]
    private ?int $lipide = null;

    #[ORM\Column]
    #[Groups(['produit:list'])]
    private ?int $proteine = null;

    #[ORM\OneToOne(inversedBy: 'characteristic', cascade: ['persist', 'remove'])]
    private ?Produit $produit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCendres(): ?string
    {
        return $this->cendres;
    }

    public function setCendres(string $cendres): self
    {
        $this->cendres = $cendres;

        return $this;
    }

    public function getEau(): ?int
    {
        return $this->eau;
    }

    public function setEau(int $eau): self
    {
        $this->eau = $eau;

        return $this;
    }

    public function getFibre(): ?int
    {
        return $this->fibre;
    }

    public function setFibre(int $fibre): self
    {
        $this->fibre = $fibre;

        return $this;
    }

    public function getGlucide(): ?int
    {
        return $this->glucide;
    }

    public function setGlucide(int $glucide): self
    {
        $this->glucide = $glucide;

        return $this;
    }

    public function getLipide(): ?int
    {
        return $this->lipide;
    }

    public function setLipide(int $lipide): self
    {
        $this->lipide = $lipide;

        return $this;
    }

    public function getProteine(): ?int
    {
        return $this->proteine;
    }

    public function setProteine(int $proteine): self
    {
        $this->proteine = $proteine;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }
}
