<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PatientRepository")
 */
class Patient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $prenom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_naissance;

    /**
     * @ORM\Column(type="boolean")
     */
    private $PMR;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Famille", mappedBy="patient", cascade={"persist"})
     * @ORM\OrderBy({"nom" = "ASC", "prenom" = "ASC"})
     */
    private $familles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Historique", mappedBy="patient")
     */
    private $historiques;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Specialite", inversedBy="patients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $specialite;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Participant", mappedBy="patient")
     */
    private $participants;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disabled;

    public function __construct()
    {
        $this->familles = new ArrayCollection();
        $this->historiques = new ArrayCollection();
        $this->participants = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    /**
     * @return Collection|Famille[]
     */
    public function getFamilles(): Collection
    {
        return $this->familles;
    }

    public function addFamille(Famille $famille): self
    {
        if (!$this->familles->contains($famille)) {
            $this->familles[] = $famille;
            $famille->setPatient($this);
        }

        return $this;
    }

    public function removeFamille(Famille $famille): self
    {
        if ($this->familles->contains($famille)) {
            $this->familles->removeElement($famille);
            // set the owning side to null (unless already changed)
            if ($famille->getPatient() === $this) {
                $famille->setPatient(null);
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
            $historique->setPatient($this);
        }

        return $this;
    }

    public function removeHistorique(Historique $historique): self
    {
        if ($this->historiques->contains($historique)) {
            $this->historiques->removeElement($historique);
            // set the owning side to null (unless already changed)
            if ($historique->getPatient() === $this) {
                $historique->setPatient(null);
            }
        }

        return $this;
    }

    public function getSpecialite(): ?Specialite
    {
        return $this->specialite;
    }

    public function setSpecialite(?Specialite $specialite): self
    {
        $this->specialite = $specialite;

        return $this;
    }

    /**
     * @return Collection|Participant[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->setPatient($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
            // set the owning side to null (unless already changed)
            if ($participant->getPatient() === $this) {
                $participant->setPatient(null);
            }
        }

        return $this;
    }

    public function getPMR(): ?bool
    {
        return $this->PMR;
    }

    public function setPMR(bool $PMR): self
    {
        $this->PMR = $PMR;

        return $this;
    }

    public function getDisabled(): ?bool
    {
        return $this->disabled;
    }

    public function setDisabled(bool $disabled): self
    {
        $this->disabled = $disabled;

        return $this;
    }
}
