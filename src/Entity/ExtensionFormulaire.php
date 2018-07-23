<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExtensionFormulaireRepository")
 */
class ExtensionFormulaire
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $cle;

    /**
     * @ORM\Column(type="text")
     */
    private $valeur;

    /**
     * @ORM\Column(type="text")
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $valeur_defaut;

    /**
     * @ORM\Column(type="text")
     */
    private $liste_valeur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $obligatoire;

    /**
     * @ORM\Column(type="integer")
     */
    private $ordre;

    /**
     * @ORM\Column(type="text")
     */
    private $regles;

    /**
     * @ORM\Column(type="smallint")
     */
    private $disabled;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Evenement", inversedBy="extensionFormulaires")
     */
    private $evenement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Produit", inversedBy="extensionFormulaires")
     */
    private $produit;

    public function getId()
    {
        return $this->id;
    }

    public function getCle(): ?string
    {
        return $this->cle;
    }

    public function setCle(string $cle): self
    {
        $this->cle = $cle;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): self
    {
        $this->valeur = $valeur;

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

    public function getValeurDefaut(): ?string
    {
        return $this->valeur_defaut;
    }

    public function setValeurDefaut(string $valeur_defaut): self
    {
        $this->valeur_defaut = $valeur_defaut;

        return $this;
    }

    public function getListeValeur(): ?string
    {
        return $this->liste_valeur;
    }

    public function setListeValeur(string $liste_valeur): self
    {
        $this->liste_valeur = $liste_valeur;

        return $this;
    }

    public function getObligatoire(): ?bool
    {
        return $this->obligatoire;
    }

    public function setObligatoire(bool $obligatoire): self
    {
        $this->obligatoire = $obligatoire;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getRegles(): ?string
    {
        return $this->regles;
    }

    public function setRegles(string $regles): self
    {
        $this->regles = $regles;

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

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): self
    {
        $this->evenement = $evenement;

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
