<?php

namespace App\Service\Event;

use App\Entity\Event;
use App\Entity\EventRegistration;
use App\Exception\EventFullException;
use App\Repository\EventRepositoryInterface;
use App\Repository\EventRegistrationRepositoryInterface;

class RegisterForEventService implements RegisterForEventServiceInterface
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

    public function register(Event $event, EventRegistration $registration): void
    {
        if ($event->getAvailableSpots() <= 0) {
            throw new EventFullException('Sorry, this event is full.');
        }

        $event->setAvailableSpots($event->getAvailableSpots() - 1);

        $this->eventRepository->save($event);
        $this->registrationRepository->save($registration);
    }
}
