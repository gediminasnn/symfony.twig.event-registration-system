<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private \DateTimeInterface $date;

    #[ORM\Column(length: 255, nullable: false)]
    private string $location;

    #[ORM\Column(nullable: false)]
    private int $availableSpots;

    /**
     * @var Collection<int, EventRegistration>
     */
    #[ORM\OneToMany(targetEntity: EventRegistration::class, mappedBy: 'event', orphanRemoval: true)]
    private Collection $eventRegistrations;

    public function __construct()
    {
        $this->eventRegistrations = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getAvailableSpots(): int
    {
        return $this->availableSpots;
    }

    public function setAvailableSpots(int $availableSpots): static
    {
        $this->availableSpots = $availableSpots;

        return $this;
    }

    /**
     * @return Collection<int, EventRegistration>
     */
    public function getEventRegistrations(): Collection
    {
        return $this->eventRegistrations;
    }

    public function addEventRegistration(EventRegistration $eventRegistration): static
    {
        if (!$this->eventRegistrations->contains($eventRegistration)) {
            $this->eventRegistrations->add($eventRegistration);
            $eventRegistration->setEvent($this);
        }

        return $this;
    }

    public function removeEventRegistration(EventRegistration $eventRegistration): static
    {
        if ($this->eventRegistrations->removeElement($eventRegistration)) {
            // set the owning side to null (unless already changed)
            if ($eventRegistration->getEvent() === $this) {
                $eventRegistration->setEvent(null);
            }
        }

        return $this;
    }
}
