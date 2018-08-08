<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SpecialiteRepository")
 */
class Specialite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $adulte;

    /**
     * @ORM\Column(type="boolean")
     */
    private $pediatrie;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $service;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $code_logistique;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etablissement", inversedBy="specialites")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etablissement;
    
    /**
     * @ORM\Column(type="smallint")
     */
    private $disabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Membre", mappedBy="specialite")
     */
    private $membres;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Historique", mappedBy="specialite")
     */
    private $historiques;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Patient", mappedBy="specialite")
     */
    private $patients;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProduitSpecialite", mappedBy="specialite")
     */
    private $produitSpecialites;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SpecialiteEvenement", mappedBy="specialite")
     */
    private $specialiteEvenements;

    public function __construct()
    {
        $this->membres = new ArrayCollection();
        $this->historiques = new ArrayCollection();
        $this->patients = new ArrayCollection();
        $this->produitSpecialites = new ArrayCollection();
        $this->specialiteEvenements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdulte(): ?bool
    {
        return $this->adulte;
    }

    public function setAdulte(bool $adulte): self
    {
        $this->adulte = $adulte;

        return $this;
    }

    public function getPediatrie(): ?bool
    {
        return $this->pediatrie;
    }

    public function setPediatrie(bool $pediatrie): self
    {
        $this->pediatrie = $pediatrie;

        return $this;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(string $service): self
    {
        $this->service = $service;

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

    public function getDisabled(): ?int
    {
        return $this->disabled;
    }

    public function setDisabled(int $disabled): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function getEtablissement(): ?Etablissement
    {
        return $this->etablissement;
    }

    public function setEtablissement(?Etablissement $etablissement): self
    {
        $this->etablissement = $etablissement;

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
            $membre->setSpecialite($this);
        }

        return $this;
    }

    public function removeMembre(Membre $membre): self
    {
        if ($this->membres->contains($membre)) {
            $this->membres->removeElement($membre);
            // set the owning side to null (unless already changed)
            if ($membre->getSpecialite() === $this) {
                $membre->setSpecialite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Historique[]
     */
    public function getHistoriques(): Collection
    {
        return $this->historiques;
    }

    public function addHistorique(Historique $historique): self
    {
        if (!$this->historiques->contains($historique)) {
            $this->historiques[] = $historique;
            $historique->setSpecialite($this);
        }

        return $this;
    }

    public function removeHistorique(Historique $historique): self
    {
        if ($this->historiques->contains($historique)) {
            $this->historiques->removeElement($historique);
            // set the owning side to null (unless already changed)
            if ($historique->getSpecialite() === $this) {
                $historique->setSpecialite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Patient[]
     */
    public function getPatients(): Collection
    {
        return $this->patients;
    }

    public function addPatient(Patient $patient): self
    {
        if (!$this->patients->contains($patient)) {
            $this->patients[] = $patient;
            $patient->setSpecialite($this);
        }

        return $this;
    }

    public function removePatient(Patient $patient): self
    {
        if ($this->patients->contains($patient)) {
            $this->patients->removeElement($patient);
            // set the owning side to null (unless already changed)
            if ($patient->getSpecialite() === $this) {
                $patient->setSpecialite(null);
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
            $produitSpecialite->setSpecialite($this);
        }

        return $this;
    }

    public function removeProduitSpecialite(ProduitSpecialite $produitSpecialite): self
    {
        if ($this->produitSpecialites->contains($produitSpecialite)) {
            $this->produitSpecialites->removeElement($produitSpecialite);
            // set the owning side to null (unless already changed)
            if ($produitSpecialite->getSpecialite() === $this) {
                $produitSpecialite->setSpecialite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SpecialiteEvenement[]
     */
    public function getSpecialiteEvenements(): Collection
    {
        return $this->specialiteEvenements;
    }

    public function addSpecialiteEvenement(SpecialiteEvenement $specialiteEvenement): self
    {
        if (!$this->specialiteEvenements->contains($specialiteEvenement)) {
            $this->specialiteEvenements[] = $specialiteEvenement;
            $specialiteEvenement->setSpecialite($this);
        }

        return $this;
    }

    public function removeSpecialiteEvenement(SpecialiteEvenement $specialiteEvenement): self
    {
        if ($this->specialiteEvenements->contains($specialiteEvenement)) {
            $this->specialiteEvenements->removeElement($specialiteEvenement);
            // set the owning side to null (unless already changed)
            if ($specialiteEvenement->getSpecialite() === $this) {
                $specialiteEvenement->setSpecialite(null);
            }
        }

        return $this;
    }

    
}
