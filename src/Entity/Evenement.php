<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\EvenementRepository")
 */
class Evenement
{

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="string", length=200)
     */
    private $nom;

    /**
     *
     * @ORM\Column(type="datetime")
     */
    public $date_debut;

    /**
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\Expression(
     *     "this.date_debut < this.date_fin",
     *     message="La date de fin doit être supérieure à la date de fin"
     * )
     */
    public $date_fin;

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $nombre_max;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeEvenement", inversedBy="evenements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $image_1;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $image_2;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $image_3;

    /**
     *
     * @ORM\Column(type="string", length=100)
     */
    private $nom_lieu;

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $numero_voie;

    /**
     *
     * @ORM\Column(type="string", length=300)
     */
    private $voie;

    /**
     *
     * @ORM\Column(type="string", length=100)
     */
    private $ville;

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $code_postal;

    /**
     *
     * @ORM\Column(type="array")
     */
    private $tranche_age;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $information_complementaire;

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     *
     * @ORM\Column(type="datetime")
     * @Assert\Expression(
     *     "this.date_fin_inscription < this.date_debut",
     *     message="La date de fin d'inscription doit être inférieure à la date de début"
     * )
     */
    public $date_fin_inscription;

    /**
     *
     * @ORM\Column(type="smallint")
     */
    private $disabled;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Temoignage", mappedBy="evenement")
     */
    private $temoignages;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ExtensionFormulaire", mappedBy="evenement", cascade={"persist"})
     * @ORM\OrderBy({"ordre" = "ASC"})
     */
    private $extensionFormulaires;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\EvenementQuestionnaire", mappedBy="evenement")
     */
    private $evenementQuestionnaires;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\SpecialiteEvenement", mappedBy="evenement", cascade={"persist"})
     */
    private $specialiteEvenements;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Participant", mappedBy="evenement")
     */
    private $participants;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Historique", mappedBy="evenement")
     */
    private $historiques;

    public function __construct()
    {
        $this->temoignages = new ArrayCollection();
        $this->extensionFormulaires = new ArrayCollection();
        $this->evenementQuestionnaires = new ArrayCollection();
        $this->specialiteEvenements = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->historiques = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getNombreMax(): ?int
    {
        return $this->nombre_max;
    }

    public function setNombreMax(int $nombre_max): self
    {
        $this->nombre_max = $nombre_max;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getNomLieu(): ?string
    {
        return $this->nom_lieu;
    }

    public function setNomLieu(string $nom_lieu): self
    {
        $this->nom_lieu = $nom_lieu;

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

    public function getTrancheAge(): ?array
    {
        return $this->tranche_age;
    }

    public function setTrancheAge(array $tranche_age): self
    {
        $this->tranche_age = $tranche_age;

        return $this;
    }

    public function getInformationComplementaire(): ?string
    {
        return $this->information_complementaire;
    }

    public function setInformationComplementaire(?string $information_complementaire): self
    {
        $this->information_complementaire = $information_complementaire;

        return $this;
    }

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(int $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDateFinInscription(): ?\DateTimeInterface
    {
        return $this->date_fin_inscription;
    }

    public function setDateFinInscription(\DateTimeInterface $date_fin_inscription): self
    {
        $this->date_fin_inscription = $date_fin_inscription;

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

    public function getType(): ?TypeEvenement
    {
        return $this->type;
    }

    public function setType(?TypeEvenement $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     *
     * @return Collection|Temoignage[]
     */
    public function getTemoignages(): Collection
    {
        return $this->temoignages;
    }

    public function addTemoignage(Temoignage $temoignage): self
    {
        if (! $this->temoignages->contains($temoignage)) {
            $this->temoignages[] = $temoignage;
            $temoignage->setEvenement($this);
        }

        return $this;
    }

    public function removeTemoignage(Temoignage $temoignage): self
    {
        if ($this->temoignages->contains($temoignage)) {
            $this->temoignages->removeElement($temoignage);
            // set the owning side to null (unless already changed)
            if ($temoignage->getEvenement() === $this) {
                $temoignage->setEvenement(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|ExtensionFormulaire[]
     */
    public function getExtensionFormulaires(): Collection
    {
        return $this->extensionFormulaires;
    }

    public function addExtensionFormulaire(ExtensionFormulaire $extensionFormulaire): self
    {
        if (! $this->extensionFormulaires->contains($extensionFormulaire)) {
            $this->extensionFormulaires[] = $extensionFormulaire;
            $extensionFormulaire->setEvenement($this);
        }

        return $this;
    }

    public function removeExtensionFormulaire(ExtensionFormulaire $extensionFormulaire): self
    {
        if ($this->extensionFormulaires->contains($extensionFormulaire)) {
            $this->extensionFormulaires->removeElement($extensionFormulaire);
            // set the owning side to null (unless already changed)
            if ($extensionFormulaire->getEvenement() === $this) {
                $extensionFormulaire->setEvenement(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|EvenementQuestionnaire[]
     */
    public function getEvenementQuestionnaires(): Collection
    {
        return $this->evenementQuestionnaires;
    }

    public function addEvenementQuestionnaire(EvenementQuestionnaire $evenementQuestionnaire): self
    {
        if (! $this->evenementQuestionnaires->contains($evenementQuestionnaire)) {
            $this->evenementQuestionnaires[] = $evenementQuestionnaire;
            $evenementQuestionnaire->setEvenement($this);
        }

        return $this;
    }

    public function removeEvenementQuestionnaire(EvenementQuestionnaire $evenementQuestionnaire): self
    {
        if ($this->evenementQuestionnaires->contains($evenementQuestionnaire)) {
            $this->evenementQuestionnaires->removeElement($evenementQuestionnaire);
            // set the owning side to null (unless already changed)
            if ($evenementQuestionnaire->getEvenement() === $this) {
                $evenementQuestionnaire->setEvenement(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|SpecialiteEvenement[]
     */
    public function getSpecialiteEvenements(): Collection
    {
        return $this->specialiteEvenements;
    }

    public function addSpecialiteEvenement(SpecialiteEvenement $specialiteEvenement): self
    {
        if (! $this->specialiteEvenements->contains($specialiteEvenement)) {
            $this->specialiteEvenements[] = $specialiteEvenement;
            $specialiteEvenement->setEvenement($this);
        }

        return $this;
    }

    public function removeSpecialiteEvenement(SpecialiteEvenement $specialiteEvenement): self
    {
        if ($this->specialiteEvenements->contains($specialiteEvenement)) {
            $this->specialiteEvenements->removeElement($specialiteEvenement);
            // set the owning side to null (unless already changed)
            if ($specialiteEvenement->getEvenement() === $this) {
                $specialiteEvenement->setEvenement(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|Participant[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (! $this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->setEvenement($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
            // set the owning side to null (unless already changed)
            if ($participant->getEvenement() === $this) {
                $participant->setEvenement(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|Historique[]
     */
    public function getHistoriques(): Collection
    {
        return $this->historiques;
    }

    public function addHistorique(Historique $historique): self
    {
        if (! $this->historiques->contains($historique)) {
            $this->historiques[] = $historique;
            $historique->setEvenement($this);
        }

        return $this;
    }

    public function removeHistorique(Historique $historique): self
    {
        if ($this->historiques->contains($historique)) {
            $this->historiques->removeElement($historique);
            // set the owning side to null (unless already changed)
            if ($historique->getEvenement() === $this) {
                $historique->setEvenement(null);
            }
        }

        return $this;
    }

    /**
     * Duplication d'un événement
     * 
     * @param Evenement $evenement
     */
    public function duplicate(Evenement $evenement)
    {
        $champsDupliques = array(
            'Nom',
            'Description',
            'InformationComplementaire',
            'NombreMax',
            'TrancheAge',
            'Statut'
        );
        
        foreach ($champsDupliques as $champ){
            $methodeGet = 'get' . $champ;
            $methodeSet = 'set' . $champ;
            
            $this->{$methodeSet}($evenement->{$methodeGet}());
        }
        
        foreach ($evenement->getExtensionFormulaires() as $extension) {
            $extensionDuplicated = new ExtensionFormulaire();
            $extensionDuplicated->duplicate($extension);
            $this->addExtensionFormulaire($extensionDuplicated);
        }
    }
}
