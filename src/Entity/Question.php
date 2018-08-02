<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
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
    private $libelle;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $libelle_top;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $libelle_bottom;

    /**
     * @ORM\Column(type="text")
     */
    private $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $valeur_defaut;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $liste_valeur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $obligatoire;

    /**
     * @ORM\Column(type="text")
     */
    private $regles;

    /**
     * @ORM\Column(type="text")
     */
    private $message_erreur;

    /**
     * @ORM\Column(type="integer")
     */
    private $ordre;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reponse", mappedBy="question")
     */
    private $reponses;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Questionnaire", inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $questionnaire;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="question")
     */
    private $children;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="children")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;
    

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getLibelleTop(): ?string
    {
        return $this->libelle_top;
    }

    public function setLibelleTop(string $libelle_top): self
    {
        $this->libelle_top = $libelle_top;

        return $this;
    }

    public function getLibelleBottom(): ?string
    {
        return $this->libelle_bottom;
    }

    public function setLibelleBottom(string $libelle_bottom): self
    {
        $this->libelle_bottom = $libelle_bottom;

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

    public function setValeurDefaut(?string $valeur_defaut): self
    {
        $this->valeur_defaut = $valeur_defaut;

        return $this;
    }

    public function getListeValeur(): ?string
    {
        return $this->liste_valeur;
    }

    public function setListeValeur(?string $liste_valeur): self
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

    public function getRegles(): ?string
    {
        return $this->regles;
    }

    public function setRegles(string $regles): self
    {
        $this->regles = $regles;

        return $this;
    }

    public function getMessageErreur(): ?string
    {
        return $this->message_erreur;
    }

    public function setMessageErreur(string $message_erreur): self
    {
        $this->message_erreur = $message_erreur;

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
     * @return Collection|Reponse[]
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setQuestion($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponses->contains($reponse)) {
            $this->reponses->removeElement($reponse);
            // set the owning side to null (unless already changed)
            if ($reponse->getQuestion() === $this) {
                $reponse->setQuestion(null);
            }
        }

        return $this;
    }

    public function getQuestionnaire(): ?Questionnaire
    {
        return $this->questionnaire;
    }

    public function setQuestionnaire(?Questionnaire $questionnaire): self
    {
        $this->questionnaire = $questionnaire;

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Question $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setQuestion($this);
        }

        return $this;
    }

    public function removeChild(Question $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getQuestion() === $this) {
                $child->setQuestion(null);
            }
        }

        return $this;
    }

    public function getQuestion(): ?self
    {
        return $this->question;
    }

    public function setQuestion(?self $question): self
    {
        $this->question = $question;

        return $this;
    }

  }