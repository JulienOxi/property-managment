<?php

namespace App\Form;

use App\Entity\Lease;
use App\Entity\Property;
use App\Enum\AccessRoleEnum;
use App\Enum\RentalFeeEnum;
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
            ->add('rentAmount')
            ->add('feeAmount')
            ->add('feeType', ChoiceType::class, [
                'choices' => RentalFeeEnum::cases(), // Liste des enums
                'choice_label' => fn(RentalFeeEnum $type) => $type->value, // Affichage du label
                'choice_value' => fn(?RentalFeeEnum $type) => $type?->name, // Utilisation du nom de l'enum pour la valeur
            ])
            ->add('parkingAmount')
            ->add('variousAmount')
            ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lease::class,
        ]);
    }
}
