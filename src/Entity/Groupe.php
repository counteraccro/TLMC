<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupeRepository")
 */
class Groupe
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
     * @ORM\Column(type="smallint")
     */
    private $disabled;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="groupe")
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GroupeMembre", mappedBy="groupe")
     */
    private $groupeMembres;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->groupeMembres = new ArrayCollection();
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

    public function getDisabled(): ?int
    {
        return $this->disabled;
    }

    public function setDisabled(int $disabled): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setGroupe($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getGroupe() === $this) {
                $message->setGroupe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GroupeMembre[]
     */
    public function getGroupeMembres(): Collection
    {
        return $this->groupeMembres;
    }

    public function addGroupeMembre(GroupeMembre $groupeMembre): self
    {
        if (!$this->groupeMembres->contains($groupeMembre)) {
            $this->groupeMembres[] = $groupeMembre;
            $groupeMembre->setGroupe($this);
        }

        return $this;
    }

    public function removeGroupeMembre(GroupeMembre $groupeMembre): self
    {
        if ($this->groupeMembres->contains($groupeMembre)) {
            $this->groupeMembres->removeElement($groupeMembre);
            // set the owning side to null (unless already changed)
            if ($groupeMembre->getGroupe() === $this) {
                $groupeMembre->setGroupe(null);
            }
        }

        return $this;
    }
}
