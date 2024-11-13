<?php

namespace App\Controller\Event;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowController extends AbstractController
{
    #[Route('/event/{id}', name: 'event_show', methods: ['GET'])]
    public function __invoke(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }
}
