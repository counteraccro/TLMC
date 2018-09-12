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
    private $image_1;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $image_2;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $image_3;

    /**
     * @ORM\Column(type="array")
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
     * @ORM\OneToMany(targetEntity="App\Entity\ExtensionFormulaire", mappedBy="produit", cascade={"persist"})
     * @ORM\OrderBy({"ordre" = "ASC"})
     */
    private $extensionFormulaires;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProduitEtablissement", mappedBy="produit", cascade={"persist"})
     */
    private $produitEtablissements;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProduitSpecialite", mappedBy="produit", cascade={"persist"})
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
        $this->produitEtablissements = new ArrayCollection();
        $this->produitSpecialites = new ArrayCollection();
        $this->produitQuestionnaires = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getImage1(): ?string
    {
        return $this->image_1;
    }

    public function setImage1(?string $image_1): self
    {
        $this->image_1 = $image_1;

        return $this;
    }

    public function getImage2(): ?string
    {
        return $this->image_2;
    }

    public function setImage2(?string $image_2): self
    {
        $this->image_2 = $image_2;

        return $this;
    }

    public function getImage3(): ?string
    {
        return $this->image_3;
    }

    public function setImage3(?string $image_3): self
    {
        $this->image_3 = $image_3;

        return $this;
    }

    public function getTrancheAge(): ?array
    {
        return $this->tranche_age;
    }

    public function setTrancheAge(array $tranche_age): self
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

    public function getDisabled(): ?int
    {
        return $this->disabled;
    }

    public function setDisabled(int $disabled): self
    {
        $this->disabled = $disabled;

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
    public function getProduitEtablissements(): Collection
    {
        return $this->produitEtablissements;
    }

    public function addProduitEtablissement(ProduitEtablissement $produitEtablissement): self
    {
        if (!$this->produitEtablissements->contains($produitEtablissement)) {
            $this->produitEtablissements[] = $produitEtablissement;
            $produitEtablissement->setProduit($this);
        }

        return $this;
    }

    public function removeProduitEtablissement(ProduitEtablissement $produitEtablissement): self
    {
        if ($this->produitEtablissements->contains($produitEtablissement)) {
            $this->produitEtablissements->removeElement($produitEtablissement);
            // set the owning side to null (unless already changed)
            if ($produitEtablissement->getProduit() === $this) {
                $produitEtablissement->setProduit(null);
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
    
}
