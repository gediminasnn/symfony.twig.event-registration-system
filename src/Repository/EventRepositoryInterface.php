<?php

namespace App\Repository;

use App\Entity\Event;

interface EventRepositoryInterface
{
    /**
     * @return Event[]
     */
    public function findAll(): array;

    public function save(Event $event): void;
}
