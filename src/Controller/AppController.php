<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    public function __construct(
        private TexterInterface $texter,
    ) {
    }
    #[Route('/', name: 'app_home')]
    public function app(ChartBuilderInterface $chartBuilder): Response
    {
        return $this->redirectToRoute('app_property_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/documentation', name: 'app_doc')]
    public function documentation(): Response
    {
        
        return $this->render('documentation.html.twig', [

        ]);
    }

    #[Route('/xy', name: 'xy')]
    public function xy(UserRepository $userRepository): JsonResponse
    {
        return new JsonResponse([
            'Users' => $userRepository->count(),
        ]);
    }
}
