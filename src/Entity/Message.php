<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $corps;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_envoi;
    
    /**
     * @ORM\Column(type="smallint")
     */
    private $disabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PieceJointe", mappedBy="message")
     */
    private $pieceJointes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Groupe", inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $groupe;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MessageLu", mappedBy="message")
     */
    private $messageLus;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Membre", inversedBy="messagesRecus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $destinataire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Membre", inversedBy="messagesEnvoyes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $expediteur;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="message")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Message", inversedBy="children")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     */
    private $message;

    /**
     * @ORM\Column(type="boolean")
     */
    private $brouillon;
    
    public function __construct()
    {
        $this->pieceJointes = new ArrayCollection();
        $this->messageLus = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function setCorps(?string $corps): self
    {
        $this->corps = $corps;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->date_envoi;
    }

    public function setDateEnvoi(\DateTimeInterface $date_envoi): self
    {
        $this->date_envoi = $date_envoi;

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

    public function getBrouillon(): ?bool
    {
        return $this->brouillon;
    }

    public function setBrouillon(bool $brouillon): self
    {
        $this->brouillon = $brouillon;

        return $this;
    }

    /**
     * @return Collection|PieceJointe[]
     */
    public function getPieceJointes(): Collection
    {
        return $this->pieceJointes;
    }

    public function addPieceJointe(PieceJointe $pieceJointe): self
    {
        if (!$this->pieceJointes->contains($pieceJointe)) {
            $this->pieceJointes[] = $pieceJointe;
            $pieceJointe->setMessage($this);
        }

        return $this;
    }

    public function removePieceJointe(PieceJointe $pieceJointe): self
    {
        if ($this->pieceJointes->contains($pieceJointe)) {
            $this->pieceJointes->removeElement($pieceJointe);
            // set the owning side to null (unless already changed)
            if ($pieceJointe->getMessage() === $this) {
                $pieceJointe->setMessage(null);
            }
        }

        return $this;
    }

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

    /**
     * @return Collection|MessageLu[]
     */
    public function getMessageLus(): Collection
    {
        return $this->messageLus;
    }

    public function addMessageLus(MessageLu $messageLus): self
    {
        if (!$this->messageLus->contains($messageLus)) {
            $this->messageLus[] = $messageLus;
            $messageLus->setMessage($this);
        }

        return $this;
    }

    public function removeMessageLus(MessageLu $messageLus): self
    {
        if ($this->messageLus->contains($messageLus)) {
            $this->messageLus->removeElement($messageLus);
            // set the owning side to null (unless already changed)
            if ($messageLus->getMessage() === $this) {
                $messageLus->setMessage(null);
            }
        }

        return $this;
    }

    public function getDestinataire(): ?Membre
    {
        return $this->destinataire;
    }

    public function setDestinataire(?Membre $destinataire): self
    {
        $this->destinataire = $destinataire;

        return $this;
    }

    public function getExpediteur(): ?Membre
    {
        return $this->expediteur;
    }

    public function setExpediteur(?Membre $expediteur): self
    {
        $this->expediteur = $expediteur;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Message $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setMessage($this);
        }

        return $this;
    }

    public function removeChild(Message $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getMessage() === $this) {
                $child->setMessage(null);
            }
        }

        return $this;
    }

    public function getMessage(): ?self
    {
        return $this->message;
    }

    public function setMessage(?self $message): self
    {
        $this->message = $message;

        return $this;
    }

    
}
