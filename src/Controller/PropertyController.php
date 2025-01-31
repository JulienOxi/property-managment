<?php

namespace App\Controller;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Enum\AccessRoleEnum;
use App\Entity\AccessControl;
use App\Form\PropertyShareType;
use App\Repository\AccessControlRepository;
use App\Service\AccessControlService;
use App\Repository\PropertyRepository;
use App\Repository\UploadFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FinancialEntryRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/app/property')]
final class PropertyController extends AbstractController
{

    private AccessControlService $accessControlService;

    public function __construct(AccessControlService $accessControlService)
    {
        $this->accessControlService = $accessControlService;
    }

    
    #[Route(name: 'app_property_index', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepository): Response
    {

        $properties = $propertyRepository->findAccessibleProperties($this->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER]);

        return $this->render('property/index.html.twig', [
            'properties' => $properties,
        ]);
    }

    #[Route('/new', name: 'app_property_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $property->setCreatedBy($this->getUser());
            
            $accessControl = new AccessControl();
            $accessControl->setGrantedUser($this->getUser())
                ->setRole(AccessRoleEnum::OWNER)
                ->setProperty($property);

            $entityManager->persist($accessControl);
            $entityManager->persist($property);
            $entityManager->flush();

            $this->addFlash('success','Félicitation, votre propriété a bien été crée !');

            return $this->redirectToRoute('app_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('property/new.html.twig', [
            'property' => $property,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_property_show', methods: ['GET'])]
    public function show(Request $request, Property $property, FinancialEntryRepository $financialEntryRepository, UploadFileRepository $uploadFileRepository): Response
    {

        $propertyCheck = $this->accessControlService->canAccessProperty($this->getUser(), $property, AccessRoleEnum::MEMBER);
        if (!$propertyCheck) {
            throw $this->createAccessDeniedException('Vous n’avez pas accès à cette propriété.');
        }

        $year = $request->get('year') ?? date('Y');
        $financialEntrys = $financialEntryRepository->findEntryByPropertyAndYear($property, $year); //selection des loyers (entrées financières)
        $financialDeposit = $financialEntryRepository->findEntryByPropertyAndYear($property, $year, true); //selection des charges (sorties financières)

        //modification de la clef en nom du mois
        $shortFinancialEntrys = [];
        foreach ($financialEntrys as $key => $value) {
            $shortFinancialEntrys[$value->getPaidAt()->format('m').'-'.$value->getCategory()->name] = $value;
        }
        $shortFinancialDeposit = [];
        foreach ($financialDeposit as $key => $value) {
            $shortFinancialDeposit[$value->getPaidAt()->format('m').'-'.$value->getCategory()->name] = $value;
        }

        // selection des hypothèques
        $shortMortgages = [];
        $mortgages = $financialEntryRepository->findMortgageByPropertyAndYear($property, $year);
        foreach ($mortgages as $key => $value) {
            $shortMortgages[$value->getPaidAt()->format('m').'-'.$value->getCategory()->name] = $value;
        }

        //selection des fichiers
        $uploadsFiles = $uploadFileRepository->findBy(['entityId' => $property->getId(), 'type' => 'document', 'entityClass' => 'Property']);
        $uploadsImages = $uploadFileRepository->findBy(['entityId' => $property->getId(), 'type' => 'image', 'entityClass' => 'Property']);

        return $this->render('property/show.html.twig', [
            'year' => $year,
            'property' => $property,
            'uploads' => null,
            'financialEntrys' => $shortFinancialEntrys,
            'financialDeposit' => $shortFinancialDeposit,
            'mortgages' => $shortMortgages,
            'uploadsFiles' => $uploadsFiles,
            'uploadsImages' => $uploadsImages
        ]);
    }

    #[Route('/{id}/edit', name: 'app_property_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        $propertyCheck = $this->accessControlService->canAccessProperty($this->getUser(), $property, AccessRoleEnum::MEMBER);
        if (!$propertyCheck) {
            throw $this->createAccessDeniedException('Vous n’avez pas accès à cette propriété.');
        }

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $property->setUpdatedBy($this->getUser())
                ->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->flush();

            $this->addFlash('success','Modification effectuée');

            return $this->redirectToRoute('app_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('property/edit.html.twig', [
            'property' => $property,
            'form' => $form,
        ]);
    }

    /**
     * Fonction permettant de partager une propriété
     * elle peut recevoir un lien de partage et le traiter si celui-ci est valide
     * elle affiche un formulaire pour le partage d'un lien sinon
     * prend en param le ID de la propriété
     */
    #[Route('/share/{id}', name: 'app_property_share', methods: ['GET', 'POST'])]
    public function share(Request $request, Property $property, AccessControlRepository $accessControlRepository, EntityManagerInterface $entityManager, UriSigner $uriSigner, MailerInterface $mailer): Response
    {
    
    /////Partie validation du lien
        //on regarde si on trouve une url de validation valide
        if ($request->isMethod('GET') && $request->query->has('_hash') && $request->query->get('_expiration') && $request->query->get('email')) {
            //check si email correspond à l'email de l'utilisateur
            if($request->query->get('email') != md5(strtoupper($this->getUser()->getEmail()))) {
                $this->addFlash('error','Erreur. L\'email d\'invitation ne correspond pas à votre email.');
                return $this->redirectToRoute('app_home');
            }

            if (!$propertyOwner = $accessControlRepository->findOneBy(['property' => $property, 'role' => AccessRoleEnum::OWNER])) {
                $this->addFlash('error', 'Erreur, le propriétaire n\'a pas été trouvé.');
                return $this->redirectToRoute('app_home');
            }
            //check de la validation du lien
                if ($uriSigner->check($request->getUri())) {
                //on control que l'utilisateur n'aille pas déjà un accès à cette propriété
                if($accessControlGranted = $accessControlRepository->findOneBy(['property' => $property, 'grantedUser' => $this->getUser()])) {
                    $this->addFlash('warning','OUPS,Vous avez déjà accès à cette propriété comme '.$accessControlGranted->getRole()->value.'.');
                    return $this->redirectToRoute('app_property_show', ['id' => $property->getId()]);
                }
                //si l'utilisateur n'a pas encore accès à la propriété on lui accorde un accès en tant que membre
                $accessControl = new AccessControl();
                $accessControl->setGrantedUser($this->getUser())
                    ->setRole(AccessRoleEnum::MEMBER)
                    ->setProperty($property);
                $entityManager->persist($accessControl);
                $entityManager->flush();

            //email au propriétaire
                $templateEmail = (new TemplatedEmail())
                ->from($this->getUser()->getEmail())
                ->to($propertyOwner->getGrantedUser()->getEmail())
                ->subject('Votre invitation a été acceptée')
                ->htmlTemplate('emails/ownerConfirmSharing.html.twig')
                    // pass variables
                ->context([
                    'owner_name' => $propertyOwner->getGrantedUser()->getProfile()->getFullName(),
                    'property_link' => $this->generateUrl('app_property_share', ['id' => $property->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                    'property_name' => $property->getType()->value.' - '.$property->getAddress()->getZipCode().' '.$property->getAddress()->getCity(),
                    'invited_user' => $this->getUser()->getProfile()->getFullName(),
                    'app_name' => $_ENV['APP_NAME']
                ]);
                $mailer->send($templateEmail);

                $this->addFlash('success','Félicitation, le partage de cette propriété a bien été effectuée, vous pouvez maintenant consulter ce bien !');

                return $this->redirectToRoute('app_property_index', [], Response::HTTP_SEE_OTHER);
            }
        }else{
    /////Partie si pas de lien valide
            //si l'utlisateur n'est pas le propriétaire du bien
            $propertyCheck = $this->accessControlService->canAccessProperty($this->getUser(), $property, AccessRoleEnum::OWNER);
            if (!$propertyCheck) {
                $this->addFlash('error','OUPS,Vous n’avez pas accès au partage de cette propriété.');
                return $this->redirect($request->headers->get('referer'));
            }
        }

        $form = $this->createForm(PropertyShareType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $name = $form->get('name')->getData();
            $email = $form->get('email')->getData();
            $hashUrl = $uriSigner->sign($this->generateUrl('app_property_share', ['id' => $property->getId(), 'email' => md5(strtoupper($email))], UrlGeneratorInterface::ABSOLUTE_URL), new \DateTimeImmutable('+1 day'));
            strtr($hashUrl, ['/' => '_', '+' => '-']);

            //envoi de l'email au destinataire
            $templateEmail = (new TemplatedEmail())
            ->from($this->getUser()->getEmail())
            ->to($email)
            ->subject('Invitation à rejoindre une propriété')
            ->htmlTemplate('emails/shareLink.html.twig')
                // pass variables
            ->context([
                'name' => $name,
                'secure_link' => $hashUrl,
                'app_name' => $_ENV['APP_NAME']
            ]);
            $mailer->send($templateEmail);

            $this->addFlash('success','Un lien de partage vient d\'être envoyé à '.$email);

            return $this->redirectToRoute('app_property_show', ['id' => $property->getId()]);
        }

        return $this->render('property/share.html.twig', [
            'property' => $property,
            'form' => $form,
        ]);
    }
}
