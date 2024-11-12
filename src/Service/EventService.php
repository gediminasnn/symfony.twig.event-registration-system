<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\EventRegistration;
use App\Exception\EventFullException;
use App\Service\EventServiceInterface;
use App\Repository\EventRepositoryInterface;
use App\Repository\EventRegistrationRepositoryInterface;

class EventService implements EventServiceInterface
{
    private EventRepositoryInterface $eventRepository;
    private EventRegistrationRepositoryInterface $registrationRepository;

    public function __construct(
        EventRepositoryInterface $eventRepository,
        EventRegistrationRepositoryInterface $registrationRepository
    ) {
        $this->eventRepository = $eventRepository;
        $this->registrationRepository = $registrationRepository;
    }

    public function listEvents(): array
    {
        return $this->eventRepository->findAll();
    }

    public function registerForEvent(Event $event, EventRegistration $registration): void
    {
        if ($event->getAvailableSpots() <= 0) {
            throw new EventFullException();
        }

        $event->setAvailableSpots($event->getAvailableSpots() - 1);

        $this->eventRepository->save($event);
        $this->registrationRepository->save($registration);
    }
}
