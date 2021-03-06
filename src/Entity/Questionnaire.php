<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\QuestionnaireRepository")
 * @UniqueEntity(
 *     fields={"slug"},
 *     errorPath="slug",
 *     message="L'url saisie est déjà utilisée pour un autre questionnaire"
 * )
 * @UniqueEntity(
 *     fields={"titre"},
 *     errorPath="titre",
 *     message="Le titre saisi est déjà utilisé pour un autre questionnaire"
 * )
 */
class Questionnaire
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
     * @ORM\Column(type="string", length=300)
     */
    private $titre;

    /**
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     *
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     *
     * @ORM\Column(type="datetime")
     */
    private $date_fin;

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $jour_relance;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    private $disabled;

    /**
     *
     * @ORM\Column(type="string", length=300)
     */
    private $slug;

    /**
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_publication;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    private $publication;

    /**
     *
     * @ORM\Column(type="text")
     */
    private $description_after_submit;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="questionnaire", cascade={"persist"})
     * @ORM\OrderBy({"ordre" = "ASC"})
     */
    private $questions;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ProduitQuestionnaire", mappedBy="questionnaire")
     */
    private $produitQuestionnaires;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\EvenementQuestionnaire", mappedBy="questionnaire")
     */
    private $evenementQuestionnaires;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->produitQuestionnaires = new ArrayCollection();
        $this->evenementQuestionnaires = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getJourRelance(): ?int
    {
        return $this->jour_relance;
    }

    public function setJourRelance(int $jour_relance): self
    {
        $this->jour_relance = $jour_relance;

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

    /**
     *
     * @return Collection|ProduitQuestionnaire[]
     */
    public function getProduitQuestionnaires(): Collection
    {
        return $this->produitQuestionnaires;
    }

    public function addProduitQuestionnaire(ProduitQuestionnaire $produitQuestionnaire): self
    {
        if (! $this->produitQuestionnaires->contains($produitQuestionnaire)) {
            $this->produitQuestionnaires[] = $produitQuestionnaire;
            $produitQuestionnaire->setQuestionnaire($this);
        }

        return $this;
    }

    public function removeProduitQuestionnaire(ProduitQuestionnaire $produitQuestionnaire): self
    {
        if ($this->produitQuestionnaires->contains($produitQuestionnaire)) {
            $this->produitQuestionnaires->removeElement($produitQuestionnaire);
            // set the owning side to null (unless already changed)
            if ($produitQuestionnaire->getQuestionnaire() === $this) {
                $produitQuestionnaire->setQuestionnaire(null);
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
            $evenementQuestionnaire->setQuestionnaire($this);
        }

        return $this;
    }

    public function removeEvenementQuestionnaire(EvenementQuestionnaire $evenementQuestionnaire): self
    {
        if ($this->evenementQuestionnaires->contains($evenementQuestionnaire)) {
            $this->evenementQuestionnaires->removeElement($evenementQuestionnaire);
            // set the owning side to null (unless already changed)
            if ($evenementQuestionnaire->getQuestionnaire() === $this) {
                $evenementQuestionnaire->setQuestionnaire(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPublication(): ?bool
    {
        return $this->publication;
    }

    public function setPublication(bool $publication): self
    {
        $this->publication = $publication;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->date_publication;
    }

    public function setDatePublication(\DateTimeInterface $date_publication): self
    {
        $this->date_publication = $date_publication;

        return $this;
    }

    public function getDescriptionAfterSubmit(): ?string
    {
        return $this->description_after_submit;
    }

    public function setDescriptionAfterSubmit(string $description_after_submit): self
    {
        $this->description_after_submit = $description_after_submit;

        return $this;
    }

    /**
     *
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (! $this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setQuestionnaire($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getQuestionnaire() === $this) {
                $question->setQuestionnaire(null);
            }
        }

        return $this;
    }

    /**
     * Clone de l'objet questionnaire sans prendre en compte les collections
     *
     * @param Questionnaire $questionnaire
     */
    public function clone(Questionnaire $questionnaire)
    {
        $vars = get_class_vars(get_class($this));

        foreach ($vars as $key => $var) {
            $value = ucwords(str_replace(array(
                '-',
                '_'
            ), ' ', $key));
            $value = str_replace(' ', '', $value);
            $value = lcfirst($value);

            if ($key == 'id') {
                continue;
            }

            $getMethode = 'get' . ucfirst($value);
            $setMethode = 'set' . ucfirst($value);

            if (! $questionnaire->{$getMethode}() instanceof Collection) {
                $this->{$setMethode}($questionnaire->{$getMethode}());
            }
        }
    }
}
