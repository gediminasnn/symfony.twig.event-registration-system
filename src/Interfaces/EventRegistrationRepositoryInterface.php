<?php

namespace App\Interfaces;

use App\Entity\EventRegistration;

interface EventRegistrationRepositoryInterface
{
    public function save(EventRegistration $registration): void;
}
