<?php

namespace App\Form;

use App\Entity\Bank;
use App\Entity\Lease;
use App\Entity\Property;
use App\Form\TenantType;
use App\Enum\RentalFeeEnum;
use App\Enum\AccessRoleEnum;
use Doctrine\ORM\QueryBuilder;
use App\Repository\BankRepository;
use App\Repository\PropertyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class LeaseType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fromAt', null, [
                'widget' => 'single_text',
            ])
            ->add('toAt', null, [
                'widget' => 'single_text',
            ])
            ->add('property', EntityType::class, [
                'class' => Property::class,
                'query_builder' => function (PropertyRepository $propertyRepository) {
                    return $propertyRepository->findAccessibleProperties($this->security->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER], false);
                },
                'choice_label' => 'name',
            ])
            ->add('tenants', CollectionType::class, [
                'entry_type' => TenantType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'attr' => [
                    'data-controller' => 'form-collection',
                    'data-form-collection-btnadd-value' => 'Ajouter un locataire',
                ]
            ])
            ->add('rentAmount', null, [
                'required' => true
            ])
            ->add('feeAmount', null, [
                'required' => true
            ])
            ->add('feeType', ChoiceType::class, [
                'choices' => RentalFeeEnum::cases(), // Liste des enums
                'choice_label' => fn(RentalFeeEnum $type) => $type->value, // Affichage du label
                'choice_value' => fn(?RentalFeeEnum $type) => $type?->name, // Utilisation du nom de l'enum pour la valeur
            ])
            ->add('parkingAmount')
            ->add('variousAmount')
            ->add('bank', EntityType::class, [
                'query_builder' => function (BankRepository $bankRepository):QueryBuilder {
                    return $bankRepository->createQueryBuilder('b')
                        ->where('b.createdBy = :createdBy')
                        ->setParameter('createdBy', $this->security->getUser());
                },
                'class' => Bank::class,
                'choice_label' => 'name',
                'required' => true,
                'label' => 'Banque pour le paiement',
            ])
            ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lease::class,
        ]);
    }
}
