<?php

namespace App\Form;

use App\Entity\Mortgage;
use Dom\Entity;
use App\Entity\Property;
use App\Entity\UploadFile;
use App\Enum\AccessRoleEnum;
use App\Enum\TransactionEnum;
use App\Entity\FinancialEntry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use App\Enum\FinancialCategoryEnum;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use App\Repository\PropertyRepository;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FinancialEntryNewType extends AbstractType
{
    private Security $security;

    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => TransactionEnum::cases(), // Liste des enums
                'choice_label' => fn(TransactionEnum $type) => $type->value, // Affichage du label
                'choice_value' => fn(?TransactionEnum $type) => $type?->name, // Utilisation du nom de l'enum pour la valeur
                'placeholder' => 'Type de transaction', // Optionnel
            ])
            ->add('amount')
            ->add('description')
            ->add('isPaid', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
            ]);

            if($options['data']->getPaidAt() === null){
                $builder
                    ->add('paidAt', DateType::class, [
                        'widget' => 'single_text',
                        'attr' => ['value' => Date('Y-m-d')],
                    ]);
            }else{
                $builder
                    ->add('paidAt', DateType::class, [
                        'widget' => 'single_text',
                        ]); 
            }

            $builder
            ->add('property', EntityType::class, [
                'class' => Property::class,
                'query_builder' => function (PropertyRepository $propertyRepository) {
                    return $propertyRepository->findAccessibleProperties($this->security->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER], false);
                },
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un bien',
            ])
            //ajout du chant Catégorie
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();
                $type = $data->getType() ?? null; // Récupère le type sélectionné
    
                $this->updateCategoryField($form, $type);
            })
            //Ajout du chant UploadFile
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {  
                $data = $event->getData();
                $form = $event->getForm();                
                $this->updateUploadFile($form, $data);
            })
            //Ajout du chant mortgage
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {  
                $data = $event->getData();
                $form = $event->getForm();                
                $this->updateMortgage($form, $data);
            })
        ;
    }

    /**
     * Met à jour le champ "Catégorie" en fonction du type de transaction sélectionné.
     *
     * @param FormInterface $form Le formulaire.
     * @param string|null   $type Le type de transaction.
     */
    private function updateCategoryField(FormInterface $form, String | TransactionEnum | null $type): void
    {
        if (null === $type) {
            $categories = null;
        }elseif ($type instanceof TransactionEnum) {
            $type = $type->name;//on recupere le nom de l'enum
        }elseif (is_string($type)) {
            $type = $type;
        }
            $categories = match ($type) {
                'INCOME' => FinancialCategoryEnum::getByType('INCOME'),
                'EXPENSE' => FinancialCategoryEnum::getByType('EXPENSE'),
                default => null,//FinancialCategoryEnum::cases(),
            };
            if(null === $categories) {
                $form->add('category', ChoiceType::class, [
                    'choices' => null,
                    'placeholder' => 'Sélectionnez un type de dépense pour commencer',
                ]);
            }else{
                $form->add('category', ChoiceType::class, [
                    'choices' => $categories,
                    'choice_label' => fn(FinancialCategoryEnum $type) => $type->value,
                    'choice_value' => fn(?FinancialCategoryEnum $type) => $type?->name,
                    'placeholder' => 'Sélectionnez une catégorie',
                ]);
            }
    }

    /**
     * Met à jour le champ "UploadFile" en fonction du type de transaction sélectionné.
     *
     * @param FormInterface $form Le formulaire.
     * @param string|null   $type Le type de transaction.
     */
    private function updateUploadFile($form, $data): void
    {
        if ($data->getProperty() !== null) {
            $files = $this->entityManager->getRepository(UploadFile::class)->findBy(['property' => $data->getProperty(), 'entityClass' => null, 'type' => 'document']);
            $form->add('uploadFile', EntityType::class, [
                    'class' => UploadFile::class,
                    'choices' => $files,
                    'choice_label' => 'description',
                    'placeholder' => 'Sélectionnez un fichier',
                    'required' => false,
                    'mapped' => false,
                ]);
        }else{
            $form->add('uploadFile', HiddenType::class, [
            'mapped' => false
            ]);
        }
    }
    

    /**
     * Met à jour du champ "mortgage" en fonction de la catégorie sélectionnée.
     *
     * @param FormInterface $form Le formulaire.
     * @param string|null   $type Le type de transaction.
     */
    private function updateMortgage($form, $data): void
    {
        if ($data->getCategory() !== null && $data->getCategory() === FinancialCategoryEnum::MORTGAGE) {
            $form->add('mortgage', EntityType::class, [
                'class' => Mortgage::class,
                'required' => true,
                'query_builder' => function (EntityRepository $entityRepository) use ($data) {
                    return $entityRepository->createQueryBuilder('e')
                        ->where('e.property = :property')
                        ->setParameter('property', $data->getProperty());
                },
                'placeholder' => 'Sélectionnez une hypothèque',
                'choice_label' => function (Mortgage $mortgage) {
                    return $mortgage->getBank()->getName() . ' - ' . $mortgage->getMortgageType()->value . ' ' . $mortgage->getRate() . '%'.' - ' . $mortgage->getAmount() . 'CHF';
                },
            ]);
        }else{
            $form->add('mortgage', HiddenType::class, [
                'mapped' => false,
            ]);
        }
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FinancialEntry::class,
        ]);
    }
}
