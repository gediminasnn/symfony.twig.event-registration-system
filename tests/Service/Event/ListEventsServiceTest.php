<?php

namespace App\Tests\Service\Event;

use App\Entity\Event;
use App\Repository\EventRepositoryInterface;
use App\Service\Event\ListEventsService;
use App\Service\Event\ListEventsServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ListEventsServiceTest extends TestCase
{
    /** @var EventRepositoryInterface&MockObject */
    private $eventRepositoryMock;

    private ListEventsServiceInterface $listEventsService;

    protected function setUp(): void
    {
        $this->eventRepositoryMock = $this->createMock(EventRepositoryInterface::class);
        $this->listEventsService = new ListEventsService($this->eventRepositoryMock);
    }

    public function testListEventsReturnsEvents(): void
    {
        // Arrange: Create sample events
        $event1 = new Event();
        $event1->setName('Event 1');

        $event2 = new Event();
        $event2->setName('Event 2');

        // Expect the findAll method to be called once and return the sample events
        $this->eventRepositoryMock
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$event1, $event2]);

        // Act: Execute the service method
        $events = $this->listEventsService->listEvents();

        // Assert: Verify the results
        $this->assertCount(2, $events);
        $this->assertSame($event1, $events[0]);
        $this->assertSame($event2, $events[1]);
    }
}
