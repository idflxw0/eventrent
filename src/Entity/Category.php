<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['equipment:list', 'equipment:detail'])]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['equipment:list', 'equipment:detail'])]
    #[Assert\NotBlank(message: 'Le nom de la catégorie est obligatoire.')]
    #[Assert\Length(max: 100, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['equipment:detail'])]
    #[Assert\Length(max: 2000, maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Equipment::class, mappedBy: 'category')]
    private Collection $equipments;

    #[ORM\ManyToMany(targetEntity: Supplier::class, inversedBy: 'categories')]
    #[ORM\JoinTable(name: 'category_supplier')]
    private Collection $suppliers;

    public function __construct()
    {
        $this->equipments = new ArrayCollection();
        $this->suppliers  = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getEquipments(): Collection { return $this->equipments; }

    public function getSuppliers(): Collection { return $this->suppliers; }

    public function addSupplier(Supplier $supplier): static
    {
        if (!$this->suppliers->contains($supplier)) {
            $this->suppliers->add($supplier);
        }
        return $this;
    }

    public function removeSupplier(Supplier $supplier): static
    {
        $this->suppliers->removeElement($supplier);
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? 'Catégorie sans nom';
    }
}
