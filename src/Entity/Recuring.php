<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecuringRepository")
 */
class Recuring
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Categories", inversedBy="recurings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Types", inversedBy="recurings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     */
    private $value;


    public function __construct()
    {
        $this->currents = new ArrayCollection();
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

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getType(): ?Types
    {
        return $this->type;
    }

    public function setType(?Types $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Collection|Currents[]
     */
    public function getCurrents(): Collection
    {
        return $this->currents;
    }

    public function addCurrent(Currents $current): self
    {
        if (!$this->currents->contains($current)) {
            $this->currents[] = $current;
            $current->setRecuring($this);
        }

        return $this;
    }

    public function removeCurrent(Currents $current): self
    {
        if ($this->currents->contains($current)) {
            $this->currents->removeElement($current);
            // set the owning side to null (unless already changed)
            if ($current->getRecuring() === $this) {
                $current->setRecuring(null);
            }
        }

        return $this;
    }
}
