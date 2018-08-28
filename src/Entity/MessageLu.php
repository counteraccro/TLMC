<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageLuRepository")
 */
class MessageLu
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Message", inversedBy="messageLus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Membre", inversedBy="messageLus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $membre;

    /**
     * @ORM\Column(type="boolean")
     */
    private $lu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $corbeille;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    public function __construct()
    {
        $this->corbeille = 0;
        $this->lu = 0;
        $this->date = new \DateTime();
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;

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

    public function getLu(): ?bool
    {
        return $this->lu;
    }

    public function setLu(bool $lu): self
    {
        $this->lu = $lu;

        return $this;
    }

    public function getCorbeille(): ?bool
    {
        return $this->corbeille;
    }

    public function setCorbeille(bool $corbeille): self
    {
        $this->corbeille = $corbeille;

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
}
