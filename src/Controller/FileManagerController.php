<?php

namespace App\Controller;

use App\Enum\AccessRoleEnum;
use App\Service\PropertyService;
use App\Repository\PropertyRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/file/manager')]
class FileManagerController extends AbstractController
{

    #[Route('/', name: 'app_file_manager_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('file_manager/index.html.twig', [
            
        ]);
    }
}
