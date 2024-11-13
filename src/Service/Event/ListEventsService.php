<?php

namespace App\Service\Event;

use App\Entity\Event;
use App\Repository\EventRepositoryInterface;

class ListEventsService implements ListEventsServiceInterface
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @return Event[]
     */
    public function listEvents(): array
    {
        return $this->eventRepository->findAll();
    }
}
