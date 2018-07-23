<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */
class Produit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     */
    private $texte;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $tranche_age;

    /**
     * @ORM\Column(type="smallint")
     */
    private $genre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $texte_2;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantite;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_envoi;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;
    
    /**
     * @ORM\Column(type="smallint")
     */
    private $disabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Temoignage", mappedBy="produit")
     */
    private $temoignages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ExtensionFormulaire", mappedBy="produit")
     */
    private $extensionFormulaires;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProduitEtablissement", mappedBy="produit")
     */
    private $etablissement;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProduitSpecialite", mappedBy="produit")
     */
    private $produitSpecialites;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProduitQuestionnaire", mappedBy="produit")
     */
    private $produitQuestionnaires;

    public function __construct()
    {
        $this->temoignages = new ArrayCollection();
        $this->extensionFormulaires = new ArrayCollection();
        $this->etablissement = new ArrayCollection();
        $this->specialite = new ArrayCollection();
        $this->produitSpecialites = new ArrayCollection();
        $this->produitQuestionnaires = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getTrancheAge(): ?string
    {
        return $this->tranche_age;
    }

    public function setTrancheAge(string $tranche_age): self
    {
        $this->tranche_age = $tranche_age;

        return $this;
    }

    public function getGenre(): ?int
    {
        return $this->genre;
    }

    public function setGenre(int $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getTexte2(): ?string
    {
        return $this->texte_2;
    }

    public function setTexte2(?string $texte_2): self
    {
        $this->texte_2 = $texte_2;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->date_envoi;
    }

    public function setDateEnvoi(\DateTimeInterface $date_envoi): self
    {
        $this->date_envoi = $date_envoi;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    /**
     * @return Collection|Temoignage[]
     */
    public function getTemoignages(): Collection
    {
        return $this->temoignages;
    }

    public function addTemoignage(Temoignage $temoignage): self
    {
        if (!$this->temoignages->contains($temoignage)) {
            $this->temoignages[] = $temoignage;
            $temoignage->setProduit($this);
        }

        return $this;
    }

    public function removeTemoignage(Temoignage $temoignage): self
    {
        if ($this->temoignages->contains($temoignage)) {
            $this->temoignages->removeElement($temoignage);
            // set the owning side to null (unless already changed)
            if ($temoignage->getProduit() === $this) {
                $temoignage->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ExtensionFormulaire[]
     */
    public function getExtensionFormulaires(): Collection
    {
        return $this->extensionFormulaires;
    }

    public function addExtensionFormulaire(ExtensionFormulaire $extensionFormulaire): self
    {
        if (!$this->extensionFormulaires->contains($extensionFormulaire)) {
            $this->extensionFormulaires[] = $extensionFormulaire;
            $extensionFormulaire->setProduit($this);
        }

        return $this;
    }

    public function removeExtensionFormulaire(ExtensionFormulaire $extensionFormulaire): self
    {
        if ($this->extensionFormulaires->contains($extensionFormulaire)) {
            $this->extensionFormulaires->removeElement($extensionFormulaire);
            // set the owning side to null (unless already changed)
            if ($extensionFormulaire->getProduit() === $this) {
                $extensionFormulaire->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProduitEtablissement[]
     */
    public function getEtablissement(): Collection
    {
        return $this->etablissement;
    }

    public function addEtablissement(ProduitEtablissement $etablissement): self
    {
        if (!$this->etablissement->contains($etablissement)) {
            $this->etablissement[] = $etablissement;
            $etablissement->setProduit($this);
        }

        return $this;
    }

    public function removeEtablissement(ProduitEtablissement $etablissement): self
    {
        if ($this->etablissement->contains($etablissement)) {
            $this->etablissement->removeElement($etablissement);
            // set the owning side to null (unless already changed)
            if ($etablissement->getProduit() === $this) {
                $etablissement->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProduitSpecialite[]
     */
    public function getProduitSpecialites(): Collection
    {
        return $this->produitSpecialites;
    }

    public function addProduitSpecialite(ProduitSpecialite $produitSpecialite): self
    {
        if (!$this->produitSpecialites->contains($produitSpecialite)) {
            $this->produitSpecialites[] = $produitSpecialite;
            $produitSpecialite->setProduit($this);
        }

        return $this;
    }

    public function removeProduitSpecialite(ProduitSpecialite $produitSpecialite): self
    {
        if ($this->produitSpecialites->contains($produitSpecialite)) {
            $this->produitSpecialites->removeElement($produitSpecialite);
            // set the owning side to null (unless already changed)
            if ($produitSpecialite->getProduit() === $this) {
                $produitSpecialite->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProduitQuestionnaire[]
     */
    public function getProduitQuestionnaires(): Collection
    {
        return $this->produitQuestionnaires;
    }

    public function addProduitQuestionnaire(ProduitQuestionnaire $produitQuestionnaire): self
    {
        if (!$this->produitQuestionnaires->contains($produitQuestionnaire)) {
            $this->produitQuestionnaires[] = $produitQuestionnaire;
            $produitQuestionnaire->setProduit($this);
        }

        return $this;
    }

    public function removeProduitQuestionnaire(ProduitQuestionnaire $produitQuestionnaire): self
    {
        if ($this->produitQuestionnaires->contains($produitQuestionnaire)) {
            $this->produitQuestionnaires->removeElement($produitQuestionnaire);
            // set the owning side to null (unless already changed)
            if ($produitQuestionnaire->getProduit() === $this) {
                $produitQuestionnaire->setProduit(null);
            }
        }

        return $this;
    }

    public function getDisabled(): ?int
    {
        return $this->disabled;
    }

    public function setDisabled(int $disabled): self
    {
        $this->disabled = $disabled;

        return $this;
    }
}
