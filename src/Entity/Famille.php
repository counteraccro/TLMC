<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FamilleRepository")
 */
class Famille
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
     * @ORM\Column(type="integer")
     */
    private $lien_famille;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $pmr;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Regex(
     *     pattern="/^(0|\+[1-9]{2,4})[1-9]([-. ]?[0-9]{2}){4}$/",
     *     message="Le numéro de téléphone est invalide"
     * )
     */
    private $numero_tel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="familles", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $disabled;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Participant", mappedBy="famille")
     */
    private $participants;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FamilleAdresse", inversedBy="familles", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $famille_adresse;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Temoignage", mappedBy="famille")
     */
    private $temoignages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FamillePatient", mappedBy="famille")
     */
    private $famillePatients;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->temoignages = new ArrayCollection();
        $this->famillePatients = new ArrayCollection();
    }


    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function getId()
    {
        return $this->id;
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

    public function getLienFamille(): ?int
    {
        return $this->lien_famille;
    }

    public function setLienFamille(int $lien_famille): self
    {
        $this->lien_famille = $lien_famille;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPmr(): ?bool
    {
        return $this->pmr;
    }

    public function setPmr(bool $pmr): self
    {
        $this->pmr = $pmr;

        return $this;
    }

    public function getNumeroTel(): ?string
    {
        return $this->numero_tel;
    }

    public function setNumeroTel(string $numero_tel): self
    {
        $this->numero_tel = $numero_tel;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

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
            $participant->setFamille($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
            // set the owning side to null (unless already changed)
            if ($participant->getFamille() === $this) {
                $participant->setFamille(null);
            }
        }

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

    public function getFamilleAdresse(): ?FamilleAdresse
    {
        return $this->famille_adresse;
    }

    public function setFamilleAdresse(?FamilleAdresse $famille_adresse): self
    {
        $this->famille_adresse = $famille_adresse;

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
            $temoignage->setFamille($this);
        }

        return $this;
    }

    public function removeTemoignage(Temoignage $temoignage): self
    {
        if ($this->temoignages->contains($temoignage)) {
            $this->temoignages->removeElement($temoignage);
            // set the owning side to null (unless already changed)
            if ($temoignage->getFamille() === $this) {
                $temoignage->setFamille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FamillePatient[]
     */
    public function getFamillePatients(): Collection
    {
        return $this->famillePatients;
    }

    public function addFamillePatient(FamillePatient $famillePatient): self
    {
        if (!$this->famillePatients->contains($famillePatient)) {
            $this->famillePatients[] = $famillePatient;
            $famillePatient->setFamille($this);
        }

        return $this;
    }

    public function removeFamillePatient(FamillePatient $famillePatient): self
    {
        if ($this->famillePatients->contains($famillePatient)) {
            $this->famillePatients->removeElement($famillePatient);
            // set the owning side to null (unless already changed)
            if ($famillePatient->getFamille() === $this) {
                $famillePatient->setFamille(null);
            }
        }

        return $this;
    }
}
