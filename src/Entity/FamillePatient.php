<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FamillePatientRepository")
 */
class FamillePatient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Famille", inversedBy="famillePatients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $famille;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="famillePatients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient;

    /**
     * @ORM\Column(type="integer")
     */
    private $lien_parente;

    public function getId()
    {
        return $this->id;
    }

    public function getFamille(): ?Famille
    {
        return $this->famille;
    }

    public function setFamille(?Famille $famille): self
    {
        $this->famille = $famille;

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

    public function getLienParente(): ?int
    {
        return $this->lien_parente;
    }

    public function setLienParente(int $lien_parente): self
    {
        $this->lien_parente = $lien_parente;

        return $this;
    }
}
