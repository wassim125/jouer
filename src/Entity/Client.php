<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $address = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $user_iduser = null;

    #[ORM\OneToMany(mappedBy: 'client_idclients', targetEntity: Clientproduct::class)]
    private Collection $clientproducts;

    public function __construct()
    {
        $this->clientproducts = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getUserIduser(): ?User
    {
        return $this->user_iduser;
    }

    public function setUserIduser(?User $user_iduser): static
    {
        $this->user_iduser = $user_iduser;

        return $this;
    }

    /**
     * @return Collection<int, Clientproduct>
     */
    public function getClientproducts(): Collection
    {
        return $this->clientproducts;
    }

    public function addClientproduct(Clientproduct $clientproduct): static
    {
        if (!$this->clientproducts->contains($clientproduct)) {
            $this->clientproducts->add($clientproduct);
            $clientproduct->setClientIdclients($this);
        }

        return $this;
    }

    public function removeClientproduct(Clientproduct $clientproduct): static
    {
        if ($this->clientproducts->removeElement($clientproduct)) {
            // set the owning side to null (unless already changed)
            if ($clientproduct->getClientIdclients() === $this) {
                $clientproduct->setClientIdclients(null);
            }
        }

        return $this;
    }




}
