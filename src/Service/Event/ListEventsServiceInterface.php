<?php

namespace App\Service\Event;

use App\Entity\Event;

interface ListEventsServiceInterface
{
    /**
     * @return Event[]
     */
    public function listEvents(): array;
}
