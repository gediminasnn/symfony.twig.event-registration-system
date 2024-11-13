<?php

namespace App\Controller\Event;

use App\Service\Event\ListEventsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    private ListEventsServiceInterface $listEventsService;

    public function __construct(ListEventsServiceInterface $listEventsService)
    {
        $this->listEventsService = $listEventsService;
    }

    #[Route('/', name: 'event_list', methods: ['GET'])]
    public function __invoke(): Response
    {
        $events = $this->listEventsService->listEvents();

        return $this->render('event/list.html.twig', [
            'events' => $events,
        ]);
    }
}
