<?php

namespace App\Repository;

use App\Entity\EventRegistration;
use App\Interfaces\EventRegistrationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventRegistration>
 */
class EventRegistrationRepository extends ServiceEntityRepository implements EventRegistrationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventRegistration::class);
    }

    public function save(EventRegistration $registration): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($registration);
        $entityManager->flush();
    }
}
