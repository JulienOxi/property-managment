<?php

namespace App\Controller;

use App\Entity\AccessControl;
use App\Form\AccessControlType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/property/accesscontrol')]
final class AccessControlController extends AbstractController
{

    #[Route('/{id}', name: 'app_access_control_delete', methods: ['POST'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function delete(Request $request, AccessControl $accessControl, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = $accessControl->getGrantedUser();
        if ($this->isCsrfTokenValid('delete'.$accessControl->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($accessControl);
            $entityManager->flush();
        }

            //envoi de l'email au destinataire
            $templateEmail = (new TemplatedEmail())
            ->from($this->getUser()->getEmail())
            ->to($user->getEmail())
            ->subject('Votre accès à une propriété à été retié')
            ->htmlTemplate('emails/removeAccessControl.html.twig')
                // pass variables
            ->context([
                'name' => $user->getProfile()->getFullName(),
                'property_name' => $accessControl->getProperty()->getName(),
                'app_name' => $_ENV['APP_NAME']
            ]);
            $mailer->send($templateEmail);

        $this->addFlash('success', 'L\'accès a bien été supprimé pour '.$user->getProfile()->getFullName().'.');

        $referer = $request->headers->get('referer');
        if ($referer) {
            return new RedirectResponse($referer);
        }else{
            return $this->redirectToRoute('app_property_index', [], Response::HTTP_SEE_OTHER);
        }   
    }
}
