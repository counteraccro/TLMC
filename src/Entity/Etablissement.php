<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EtablissementRepository")
 */
class Etablissement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero_voie;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $voie;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $ville;

    /**
     * @ORM\Column(type="integer")
     */
    private $code_postal;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $code_logistique;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $region;

    /**
     * @ORM\Column(type="smallint")
     */
    private $nb_lit;

    /**
     * @ORM\Column(type="smallint")
     */
    private $statut_convention;

    /**
     * @ORM\Column(type="date")
     */
    private $date_collaboration;
    
    /**
     * @ORM\Column(type="smallint")
     */
    private $disabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Specialite", mappedBy="etablissement")
     */
    private $specialites;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Membre", mappedBy="etablissement")
     */
    private $membres;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProduitEtablissement", mappedBy="etablissement")
     */
    private $produitEtablissements;

    public function __construct()
    {
        $this->specialites = new ArrayCollection();
        $this->membres = new ArrayCollection();
        $this->produits = new ArrayCollection();
        $this->produitEtablissements = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNumeroVoie(): ?int
    {
        return $this->numero_voie;
    }

    public function setNumeroVoie(int $numero_voie): self
    {
        $this->numero_voie = $numero_voie;

        return $this;
    }

    public function getVoie(): ?string
    {
        return $this->voie;
    }

    public function setVoie(string $voie): self
    {
        $this->voie = $voie;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->code_postal;
    }

    public function setCodePostal(int $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getCodeLogistique(): ?string
    {
        return $this->code_logistique;
    }

    public function setCodeLogistique(string $code_logistique): self
    {
        $this->code_logistique = $code_logistique;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getNbLit(): ?int
    {
        return $this->nb_lit;
    }

    public function setNbLit(int $nb_lit): self
    {
        $this->nb_lit = $nb_lit;

        return $this;
    }

    public function getStatutConvention(): ?int
    {
        return $this->statut_convention;
    }

    public function setStatutConvention(int $statut_convention): self
    {
        $this->statut_convention = $statut_convention;

        return $this;
    }

    public function getDateCollaboration(): ?\DateTimeInterface
    {
        return $this->date_collaboration;
    }

    public function setDateCollaboration(\DateTimeInterface $date_collaboration): self
    {
        $this->date_collaboration = $date_collaboration;

        return $this;
    }

    /**
     * @return Collection|Specialite[]
     */
    public function getSpecialites(): Collection
    {
        return $this->specialites;
    }

    public function addSpecialite(Specialite $specialite): self
    {
        if (!$this->specialites->contains($specialite)) {
            $this->specialites[] = $specialite;
            $specialite->setEtablissement($this);
        }

        return $this;
    }

    public function removeSpecialite(Specialite $specialite): self
    {
        if ($this->specialites->contains($specialite)) {
            $this->specialites->removeElement($specialite);
            // set the owning side to null (unless already changed)
            if ($specialite->getEtablissement() === $this) {
                $specialite->setEtablissement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Membre[]
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(Membre $membre): self
    {
        if (!$this->membres->contains($membre)) {
            $this->membres[] = $membre;
            $membre->setEtablissement($this);
        }

        return $this;
    }

    public function removeMembre(Membre $membre): self
    {
        if ($this->membres->contains($membre)) {
            $this->membres->removeElement($membre);
            // set the owning side to null (unless already changed)
            if ($membre->getEtablissement() === $this) {
                $membre->setEtablissement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProduitEtablissement[]
     */
    public function getProduits(): Collection
    {
        return $this->produitEtablissements;
    }

    public function addProduit(ProduitEtablissement $produitEtablissement): self
    {
        if (!$this->produitEtablissements->contains($produitEtablissement)) {
            $this->produitEtablissements[] = $produitEtablissement;
            $produitEtablissement->setEtablissement($this);
        }

        return $this;
    }

    public function removeProduit(ProduitEtablissement $produitEtablissement): self
    {
        if ($this->produitEtablissements->contains($produitEtablissement)) {
            $this->produitEtablissements->removeElement($produitEtablissement);
            // set the owning side to null (unless already changed)
            if ($produitEtablissement->getEtablissement() === $this) {
                $produitEtablissement->setEtablissement(null);
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
            $produitEtablissement->setEtablissement($this);
        }

        return $this;
    }

    public function removeProduitEtablissement(ProduitEtablissement $produitEtablissement): self
    {
        if ($this->produitEtablissements->contains($produitEtablissement)) {
            $this->produitEtablissements->removeElement($produitEtablissement);
            // set the owning side to null (unless already changed)
            if ($produitEtablissement->getEtablissement() === $this) {
                $produitEtablissement->setEtablissement(null);
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
