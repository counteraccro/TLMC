<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MembreRepository")
 */
class Membre implements AdvancedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="text")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $numero_tel;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $fonction;

    /**
     * @ORM\Column(type="smallint")
     */
    private $decideur;

    /**
     * @ORM\Column(type="smallint")
     */
    private $annuaire;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $signature;

    /**
     * @ORM\Column(type="smallint")
     */
    private $disabled;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Specialite", inversedBy="membres")
     */
    private $specialite;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etablissement", inversedBy="membres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etablissement;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Historique", mappedBy="membre")
     */
    private $historiques;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Temoignage", mappedBy="membre")
     */
    private $temoignages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reponse", mappedBy="membre")
     */
    private $reponses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GroupeMembre", mappedBy="membre")
     */
    private $groupeMembres;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MessageLu", mappedBy="membre")
     */
    private $messageLus;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="destinataire")
     */
    private $messagesRecus;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="expediteur")
     */
    private $messagesEnvoyes;

    /**
     * @ORM\Column(type="string", length=150, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salt;
    
    public function __construct()
    {
        $this->historiques = new ArrayCollection();
        $this->temoignages = new ArrayCollection();
        $this->reponses = new ArrayCollection();
        $this->groupeMembres = new ArrayCollection();
        $this->messageLus = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->messagesRecus = new ArrayCollection();
        $this->messagesEnvoyes = new ArrayCollection();
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getNumeroTel(): ?string
    {
        return $this->numero_tel;
    }

    public function setNumeroTel(?string $numero_tel): self
    {
        $this->numero_tel = $numero_tel;

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

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getDecideur(): ?int
    {
        return $this->decideur;
    }

    public function setDecideur(int $decideur): self
    {
        $this->decideur = $decideur;

        return $this;
    }

    public function getAnnuaire(): ?int
    {
        return $this->annuaire;
    }

    public function setAnnuaire(int $annuaire): self
    {
        $this->annuaire = $annuaire;

        return $this;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(string $signature): self
    {
        $this->signature = $signature;

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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

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

    public function getEtablissement(): ?Etablissement
    {
        return $this->etablissement;
    }

    public function setEtablissement(?Etablissement $etablissement): self
    {
        $this->etablissement = $etablissement;

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
            $historique->setMembre($this);
        }

        return $this;
    }

    public function removeHistorique(Historique $historique): self
    {
        if ($this->historiques->contains($historique)) {
            $this->historiques->removeElement($historique);
            // set the owning side to null (unless already changed)
            if ($historique->getMembre() === $this) {
                $historique->setMembre(null);
            }
        }

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
            $temoignage->setMembre($this);
        }

        return $this;
    }

    public function removeTemoignage(Temoignage $temoignage): self
    {
        if ($this->temoignages->contains($temoignage)) {
            $this->temoignages->removeElement($temoignage);
            // set the owning side to null (unless already changed)
            if ($temoignage->getMembre() === $this) {
                $temoignage->setMembre(null);
            }
        }

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
            $reponse->setMembre($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponses->contains($reponse)) {
            $this->reponses->removeElement($reponse);
            // set the owning side to null (unless already changed)
            if ($reponse->getMembre() === $this) {
                $reponse->setMembre(null);
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
            $groupeMembre->setMembre($this);
        }

        return $this;
    }

    public function removeGroupeMembre(GroupeMembre $groupeMembre): self
    {
        if ($this->groupeMembres->contains($groupeMembre)) {
            $this->groupeMembres->removeElement($groupeMembre);
            // set the owning side to null (unless already changed)
            if ($groupeMembre->getMembre() === $this) {
                $groupeMembre->setMembre(null);
            }
        }

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
            $messageLus->setMembre($this);
        }

        return $this;
    }

    public function removeMessageLus(MessageLu $messageLus): self
    {
        if ($this->messageLus->contains($messageLus)) {
            $this->messageLus->removeElement($messageLus);
            // set the owning side to null (unless already changed)
            if ($messageLus->getMembre() === $this) {
                $messageLus->setMembre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesRecus(): Collection
    {
        return $this->messagesRecus;
    }

    public function addMessagesRecus(Message $messagesRecus): self
    {
        if (!$this->messagesRecus->contains($messagesRecus)) {
            $this->messagesRecus[] = $messagesRecus;
            $messagesRecus->setDestinataire($this);
        }

        return $this;
    }

    public function removeMessagesRecus(Message $messagesRecus): self
    {
        if ($this->messagesRecus->contains($messagesRecus)) {
            $this->messagesRecus->removeElement($messagesRecus);
            // set the owning side to null (unless already changed)
            if ($messagesRecus->getDestinataire() === $this) {
                $messagesRecus->setDestinataire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesEnvoyes(): Collection
    {
        return $this->messagesEnvoyes;
    }

    public function addMessagesEnvoye(Message $messagesEnvoye): self
    {
        if (!$this->messagesEnvoyes->contains($messagesEnvoye)) {
            $this->messagesEnvoyes[] = $messagesEnvoye;
            $messagesEnvoye->setExpediteur($this);
        }

        return $this;
    }

    public function removeMessagesEnvoye(Message $messagesEnvoye): self
    {
        if ($this->messagesEnvoyes->contains($messagesEnvoye)) {
            $this->messagesEnvoyes->removeElement($messagesEnvoye);
            // set the owning side to null (unless already changed)
            if ($messagesEnvoye->getExpediteur() === $this) {
                $messagesEnvoye->setExpediteur(null);
            }
        }

        return $this;
    }
    public function isAccountNonExpired()
    {
       return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function eraseCredentials()
    {
        return true;
    }

    public function isEnabled()
    {
        if($this->getDisabled() == 1)
        {
            return false;
        }
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }


}
