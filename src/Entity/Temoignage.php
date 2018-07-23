<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TemoignageRepository")
 */
class Temoignage
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
    private $titre;

    /**
     * @ORM\Column(type="text")
     */
    private $corps;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $prenom_temoin;

    /**
     * @ORM\Column(type="integer")
     */
    private $lien_parente;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $ville;

    /**
     * @ORM\Column(type="integer")
     */
    private $age;

    /**
     * @ORM\Column(type="smallint")
     */
    private $disabled;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Evenement", inversedBy="temoignages")
     */
    private $evenement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Produit", inversedBy="temoignages")
     */
    private $produit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Membre", inversedBy="temoignages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $membre;

    public function getId()
    {
        return $this->id;
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

    public function getCorps(): ?string
    {
        return $this->corps;
    }

    public function setCorps(string $corps): self
    {
        $this->corps = $corps;

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

    public function getPrenomTemoin(): ?string
    {
        return $this->prenom_temoin;
    }

    public function setPrenomTemoin(string $prenom_temoin): self
    {
        $this->prenom_temoin = $prenom_temoin;

        return $this;
    }

    public function getLienParente(): ?int
    {
        return $this->lien_parente;
    }

    public function setLienParente(int $lien_parente): self
    {
        $this->lien_parente = $lien_parente;

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

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

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

    public function getMembre(): ?Membre
    {
        return $this->membre;
    }

    public function setMembre(?Membre $membre): self
    {
        $this->membre = $membre;

        return $this;
    }
}
