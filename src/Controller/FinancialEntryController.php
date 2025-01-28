<?php

namespace App\Controller;

use App\Entity\Property;
use App\Enum\AccessRoleEnum;
use App\Service\DateService;
use App\Service\EnumService;
use App\Enum\TransactionEnum;
use App\Entity\FinancialEntry;
use App\Form\FinancialEntryType;
use App\Enum\FinancialCategoryEnum;
use App\Form\TransactionFilterType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\FinancialEntryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/app/financialentry')]
final class FinancialEntryController extends AbstractController
{
    #[Route(name: 'app_financial_entry_index', methods: ['GET', 'POST'])]
    public function index(Request $request, FinancialEntryRepository $financialEntryRepository, PropertyRepository $propertyRepository, PaginatorInterface $paginator, CsrfTokenManagerInterface $csrfTokenManager): Response
    {

        //formulaire de recherche avec récupération des paramètres de l'url pour préremplire le formulaire
        $caterories = explode(':', $request->query->get('category'));//on récupère les catégories
        $caterories = array_map(fn($category) => FinancialCategoryEnum::fromName($category), $caterories); //on les transforme en enum
        
        // Récupérer les propriétés accessibles par l'utilisateur
        $accessibleProperties = $propertyRepository->findAccessibleProperties($this->getUser(), AccessRoleEnum::MEMBER);

        // Créer le formulaire
        $form = $this->createForm(TransactionFilterType::class, [
            'property' => $request->query->get('property') ? $propertyRepository->find($request->query->get('property')) : null,
            'category' => $request->query->get('category') ? $caterories : null,
            'type' => $request->query->get('type') ? TransactionEnum::fromName($request->query->get('type')) : null,
        ], [
            'accessible_properties' => $accessibleProperties, // Passer les propriétés accessibles
        ]);

        $form->handleRequest($request);

        //si le formulaire est soumis et valide on redirige vers la page avec les paramètres dans l'url
        if ($form->isSubmitted() && $form->isValid()) {

            $properties = $form->get('property')->getData();
            $categories = $form->get('category')->getData();
            $types = $form->get('type')->getData();

            $categories = array_map(fn($category) => $category->name, $categories);
            $categories = implode(':', $categories);

                    // Redirige avec les paramètres dans l'URL
        return $this->redirectToRoute('app_financial_entry_index', [
            'property' => $properties ? $properties->getId() : null,
            'category' => $categories, // Convertir tableau -> chaîne
            'type' => $types->name,
        ]);

        }

        //on récupère les paramètres de l'url pour les passer à la requête
        $request->query->get('property') ? $property = $request->query->get('property') : $property = null;
        if($request->query->get('category')) {
            $caterories = explode(':', $request->query->get('category'));//on récupère les catégories
            $caterories = array_map(fn($category) => FinancialCategoryEnum::fromName($category), $caterories); //on les transforme en enum
        }
        $request->query->get('type') ? $type = $request->query->get('type') : $type = null;
        if($property && $caterories && $type){
            $query = $financialEntryRepository->findByPropertiesAndCategoriesAndTypes($this->getUser(), AccessRoleEnum::OWNER, $propertyRepository->find($property), $caterories, TransactionEnum::fromName($type));
        }else{
            $query = $financialEntryRepository->findByPropertiesAndCategoriesAndTypes($this->getUser(), AccessRoleEnum::OWNER, null, $caterories);
        }

        $financialEntries = $paginator->paginate(
            $query,
            $request->query->get('page') ?? 1, // Numéro de page
            10 // Nombre de résultats par page
        );

        return $this->render('financial_entry/index.html.twig', [
            'financial_entries' => $financialEntries,
            'form' => $form->createView(),
            'queryParams' => $request->query->all(),
        ]);
    }

    #[Route('/new', name: 'app_financial_entry_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $financialEntry = new FinancialEntry();
        $form = $this->createForm(FinancialEntryType::class, $financialEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $financialEntry->setCreatedBy($this->getUser());
            $entityManager->persist($financialEntry);
            $entityManager->flush();

            $this->addFlash('success', 'La transaction a bien été ajouté');

            return $this->redirectToRoute('app_financial_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('financial_entry/new.html.twig', [
            'financial_entry' => $financialEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_financial_entry_show', methods: ['GET'])]
    public function show(FinancialEntry $financialEntry): Response
    {
        return $this->render('financial_entry/show.html.twig', [
            'financial_entry' => $financialEntry,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_financial_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FinancialEntry $financialEntry, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        $session = $request->getSession(); 
        $form = $this->createForm(FinancialEntryType::class, $financialEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $referer = $session->get('referer', null); // Rediriger vers le referer si disponible, sinon vers une route par défaut 
            if ($referer) 
            { 
                    return new RedirectResponse($referer); 
            } else { 
                return $this->redirectToRoute('app_financial_entry_index', [], Response::HTTP_SEE_OTHER);
            }
        }else{
                // Enregistrer le referer dans la session 
                $request = $requestStack->getCurrentRequest(); 
                $referer = $request->headers->get('referer');
                $session->set('referer', $referer);
        }

        return $this->render('financial_entry/edit.html.twig', [
            'financial_entry' => $financialEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_financial_entry_delete', methods: ['POST'])]
    public function delete(Request $request, FinancialEntry $financialEntry, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$financialEntry->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($financialEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_financial_entry_index', [], Response::HTTP_SEE_OTHER);
    }

    
    #[Route('/generate/from_property_rent/{property_id}', name: 'app_financial_entry_generate_from_property_rent', methods: ['POST'])]
    public function grnerateFromPropertyRent(FinancialEntryRepository $financialEntryRepository, Request $request, CsrfTokenManagerInterface $csrfTokenManager, EntityManagerInterface $em, DateService $dateService, EnumService $enumService, int $property_id = 0): Response
    {
        $token = $request->request->get('_csrf_token');

        // Vérifier si le token est valide
        if (!$csrfTokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('generate_from_property_rent_form', $token))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }        

        $property = $em->getRepository(Property::class)->find($property_id);
        if (!$property && $property_id != 0) {
            throw $this->createNotFoundException('Property not found');
        }
        
        // Résumé logique
        
        //     Récupère tous les contrats de location pour une propriété.
        //     Identifie ceux qui sont actuellement actifs en fonction des dates.
        //     Calcule le nombre de mois pleins pour chaque contrat actif jusque à aujourd'hui.
        //     Recherche un paiements financiers effectués entre 2 dates avec les type et catégories appropriées.
        //     Vérifie si les paiements sont corrects (en temps voulu et pour des catégories spécifiques).
        //     Créer eventuellement un loyer.

                $actualRent = []; //contrat de Loyer qui sont d'actualité (entre 2 dates)
                $fullMonth = []; //nombre de mois dans l'intérval
                foreach ($property->getPropertyRents() as $key => $rent) {
                    $dates = $dateService->returnDatesBetweenTwo($rent->getFromAt(), $rent->getEndedAt(), 'Y-m-d');
                    if(in_array(date('Y-m-d'), $dates)){ //on check si l'entrée est d'actualité
                        $actualRent[$rent->getType()->name] =  $rent;
                        $fullMonth[$rent->getType()->name] = $dateService->countFullMonthsBetweenDates($rent->getFromAt(), new \DateTimeImmutable('+1 month'));
                    }
                }


                $createdEntry = 0;
                $createdDeposit = 0;
                //on récupère les entrée dejà payé/validée
                foreach ($actualRent as $key => $rent) {
                    foreach ($fullMonth[$key] as $monthsAndYear) {//récupère le type de loyer $key::RENT|PARKING|CHARGE et tous les mois
                        foreach($monthsAndYear as $monthAndYear){ //récupère tous les mois de l'intervalle
                            //defini la periode de recherche (entre le 25 du mois d'avant et le 5 du mois en cours)
                            $withinRentPaymentDates = $dateService->withinRentPaymentPeriod($monthAndYear['month'], $monthAndYear['year']);
                            //on regarde si on trouve une entrée dans le laps de temps dans la même catégory et le même type
                            $financialEntry = $financialEntryRepository->findOneBetweenTwoDates($rent->getproperty(), $withinRentPaymentDates['start'], $withinRentPaymentDates['end'], TransactionEnum::INCOME, $enumService->mapPropertyRentToFinancialCategory($key));
                            $financialDeposit = $financialEntryRepository->findOneBetweenTwoDates($rent->getproperty(),$withinRentPaymentDates['start'], $withinRentPaymentDates['end'], TransactionEnum::EXPENSE, FinancialCategoryEnum::CHARGES_DEPOSIT);

                            //si il n'y a pas de loyer on le créer
                            if(!$financialEntry && $enumService->mapPropertyRentToFinancialCategory($key) != FinancialCategoryEnum::CHARGES_DEPOSIT){
                                $financialEntry = new FinancialEntry();
                                $financialEntry->setType(TransactionEnum::INCOME);
                                $financialEntry->setPaidAt(new \DateTimeImmutable('01.'.$monthAndYear['month'].'.'.$monthAndYear['year']));
                                $financialEntry->setCategory($enumService->mapPropertyRentToFinancialCategory($key));
                                $financialEntry->setAmount($rent->getMonthlyPrice());
                                $financialEntry->setDescription($rent->getType()->value.' '.$monthAndYear['month'].' '.$monthAndYear['year']);
                                $financialEntry->setProperty($rent->getProperty());
                                $financialEntry->setCreatedBy($this->getUser());
                                $financialEntry->setIsPaid(true);
                                $financialEntry->setTenant($rent->getTenant());
                                $financialEntry->setBank($rent->getProperty()->getBank());

                                $em->persist($financialEntry);
                                $createdEntry++;
                            }
                            //si il n'y a pas de charges on les crée
                            if(!$financialDeposit && $enumService->mapPropertyRentToFinancialCategory($key) == FinancialCategoryEnum::CHARGES_DEPOSIT){
                                $financialDeposit = new FinancialEntry();
                                $financialDeposit->setType(TransactionEnum::EXPENSE);
                                $financialDeposit->setPaidAt(new \DateTimeImmutable('01.'.$monthAndYear['month'].'.'.$monthAndYear['year']));
                                $financialDeposit->setCategory(FinancialCategoryEnum::CHARGES_DEPOSIT);
                                $financialDeposit->setAmount($rent->getMonthlyPrice());
                                $financialDeposit->setDescription($rent->getType()->value.' '.$monthAndYear['month'].' '.$monthAndYear['year']);
                                $financialDeposit->setProperty($rent->getProperty());
                                $financialDeposit->setCreatedBy($this->getUser());
                                $financialDeposit->setIsPaid(true);
                                $financialDeposit->setTenant($rent->getTenant());
                                $financialDeposit->setBank($rent->getProperty()->getBank());

                                $em->persist($financialDeposit);
                                $createdDeposit++;
                            }
                        }
                    }
                }

                //si il y a des loyer à créer on flush
                if($createdEntry > 0 || $createdDeposit > 0){
                    $em->flush();
                    $this->addFlash('success', $createdEntry.' loyers on correctement été générer !');
                    $this->addFlash('success', $createdDeposit.' acomptes de charges on correctement été générer !');
                }else{
                    $this->addFlash('warning', 'Aucun loyer à genérer !');
                }
        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }
}
