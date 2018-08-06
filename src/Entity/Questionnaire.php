<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionnaireRepository")
 */
class Questionnaire
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
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_fin;

    /**
     * @ORM\Column(type="integer")
     */
    private $jour_relance;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="questionnaire")
     * @ORM\OrderBy({"ordre" = "ASC"})
     */
    private $questions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProduitQuestionnaire", mappedBy="questionnaire")
     */
    private $produitQuestionnaires;

    /**
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
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
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
     * @return Collection|EvenementQuestionnaire[]
     */
    public function getEvenementQuestionnaires(): Collection
    {
        return $this->evenementQuestionnaires;
    }

    public function addEvenementQuestionnaire(EvenementQuestionnaire $evenementQuestionnaire): self
    {
        if (!$this->evenementQuestionnaires->contains($evenementQuestionnaire)) {
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
}
