<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Notifier\Message\DesktopMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    public function __construct(
        private TexterInterface $texter,
    ) {
    }
    #[Route('/app', name: 'app_home')]
    public function app(): Response
    {
        return $this->redirectToRoute('app_property_index');
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_home');
    }
}
