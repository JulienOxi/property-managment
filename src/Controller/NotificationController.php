<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NotificationController extends AbstractController
{

    // Notification 
        // Fin de bail
        // Fin d'hypotèque
        // Status des loyers
        // Génération des loyers prête

        
    #[Route('/notification', name: 'app_notification')]
    public function index(): Response
    {
        
    }
}
