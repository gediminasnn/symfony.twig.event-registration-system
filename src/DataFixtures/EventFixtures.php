<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $event1 = new Event();
        $event1->setName('Symfony Conference');
        $event1->setDate(new \DateTime('2024-11-01 10:00'));
        $event1->setLocation('New York');
        $event1->setAvailableSpots(100);

        $manager->persist($event1);

        $event2 = new Event();
        $event2->setName('PHP Meetup');
        $event2->setDate(new \DateTime('2024-12-15 18:00'));
        $event2->setLocation('San Francisco');
        $event2->setAvailableSpots(50);

        $manager->persist($event2);

        $manager->flush();
    }
}
