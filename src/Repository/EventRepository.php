<?php

namespace App\Repository;

use App\Entity\Event;
use App\Interfaces\EventRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository implements EventRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

    public function save(Event $event): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($event);
        $entityManager->flush();
    }
}
