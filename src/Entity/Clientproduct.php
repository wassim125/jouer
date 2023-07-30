<?php

namespace App\Entity;

use App\Repository\ClientproductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

#[ORM\Entity(repositoryClass: ClientproductRepository::class)]
class Clientproduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 50)]
    private ?string $CustomerName ;

    #[ORM\Column(length: 255)]
    private ?string $CustomerAddress ;

    #[ORM\Column(length: 255)]
    private ?string $customerEmail = null;



    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'clientproductselect')]
    private Collection $products;

    #[ORM\Column]
    private ?int $NumeroTelephone = null;

    public function __construct()
    {
        $this->favoris = new ArrayCollection();
        $this->products = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


// Add the corresponding getter and setter for the $products property
// ...



    public function getCustomerName(): ?string
    {
        return $this->CustomerName;
    }

    public function setCustomerName(string $CustomerName): static
    {
        $this->CustomerName = $CustomerName;

        return $this;
    }

    public function getCustomerAddress(): ?string
    {
        return $this->CustomerAddress;
    }

    public function setCustomerAddress(string $CustomerAddress): static
    {
        $this->CustomerAddress = $CustomerAddress;

        return $this;
    }

public function getCustomerEmail(): ?string
{
    return $this->customerEmail;
}

public function setCustomerEmail(string $customerEmail): static
{
    $this->customerEmail = $customerEmail;

    return $this;
}


//}

///**
// * @return Collection<int, Product>
// */
//public function getFavoris(): Collection
//{
//    return $this->favoris;
//}
//
//public function addFavori(Product $favori): static
//{
//    if (!$this->favoris->contains($favori)) {
//        $this->favoris->add($favori);
//    }
//
//    return $this;
//}
//
//public function removeFavori(Product $favori): static
//{
//    $this->favoris->removeElement($favori);
//
//    return $this;
//}

/**
 * @return Collection<int, Product>
 */
public function getProducts(): Collection
{
    return $this->products;
}

public function addProduct(Product $product): static
{
    if (!$this->products->contains($product)) {
        $this->products->add($product);
        $product->addClientproductselect($this);
    }

    return $this;
}

public function removeProduct(Product $product): static
{
    if ($this->products->removeElement($product)) {
        $product->removeClientproductselect($this);
    }

    return $this;
}

public function getNumeroTelephone(): ?int
{
    return $this->NumeroTelephone;
}

public function setNumeroTelephone(int $NumeroTelephone): static
{
    $this->NumeroTelephone = $NumeroTelephone;

    return $this;
}



}
