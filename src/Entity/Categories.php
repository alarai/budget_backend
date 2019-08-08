<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoriesRepository")
 */
class Categories
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Recuring", mappedBy="category")
     */
    private $recurings;

    public function __construct()
    {
        $this->recurings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Recuring[]
     */
    public function getRecurings(): Collection
    {
        return $this->recurings;
    }

    public function addRecuring(Recuring $recuring): self
    {
        if (!$this->recurings->contains($recuring)) {
            $this->recurings[] = $recuring;
            $recuring->setCategory($this);
        }

        return $this;
    }

    public function removeRecuring(Recuring $recuring): self
    {
        if ($this->recurings->contains($recuring)) {
            $this->recurings->removeElement($recuring);
            // set the owning side to null (unless already changed)
            if ($recuring->getCategory() === $this) {
                $recuring->setCategory(null);
            }
        }

        return $this;
    }
}
