<?php

namespace App\Repository;

use App\Entity\EventRegistration;

interface EventRegistrationRepositoryInterface
{
    public function save(EventRegistration $registration): void;
}
