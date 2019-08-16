<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TypesRepository")
 */
class Types
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
     * @ORM\OneToMany(targetEntity="App\Entity\Recuring", mappedBy="type")
     */
    private $recurings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Currents", mappedBy="type")
     */
    private $currents;

    /**
     * @ORM\Column(type="boolean")
     */
    private $useForHistory;

    public function __construct()
    {
        $this->recurings = new ArrayCollection();
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
            $recuring->setTypes($this);
        }

        return $this;
    }

    public function removeRecuring(Recuring $recuring): self
    {
        if ($this->recurings->contains($recuring)) {
            $this->recurings->removeElement($recuring);
            // set the owning side to null (unless already changed)
            if ($recuring->getTypes() === $this) {
                $recuring->setTypes(null);
            }
        }

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
            $current->setType($this);
        }

        return $this;
    }

    public function removeCurrent(Currents $current): self
    {
        if ($this->currents->contains($current)) {
            $this->currents->removeElement($current);
            // set the owning side to null (unless already changed)
            if ($current->getType() === $this) {
                $current->setType(null);
            }
        }

        return $this;
    }

    public function getUseForHistory(): ?bool
    {
        return $this->useForHistory;
    }

    public function setUseForHistory(bool $useForHistory): self
    {
        $this->useForHistory = $useForHistory;

        return $this;
    }
}
