<?php

namespace App\Service\Event;

use App\Entity\Event;
use App\Entity\EventRegistration;

interface RegisterForEventServiceInterface
{
    public function register(Event $event, EventRegistration $registration): void;
}
