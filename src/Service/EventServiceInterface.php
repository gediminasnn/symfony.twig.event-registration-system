<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\EventRegistration;

interface EventServiceInterface
{
    /**
     * @return Event[]
     */
    public function listEvents(): array;

    public function registerForEvent(Event $event, EventRegistration $registration): void;
}
