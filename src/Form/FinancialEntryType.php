<?php

namespace App\Form;

use App\Entity\Bank;
use App\Entity\User;
use App\Entity\Property;
use App\Enum\AccessRoleEnum;
use App\Enum\TransactionEnum;
use App\Entity\FinancialEntry;
use App\Enum\FinancialCategoryEnum;
use App\Repository\PropertyRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class FinancialEntryType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
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
            ->add('category', ChoiceType::class, [
                'choices' => FinancialCategoryEnum::cases(), // Liste des enums
                'choice_label' => fn(FinancialCategoryEnum $type) => $type->value, // Affichage du label
                'choice_value' => fn(?FinancialCategoryEnum $type) => $type?->name, // Utilisation du nom de l'enum pour la valeur
                'placeholder' => 'Sélectionnez une catégorie', // Optionnel
            ])
            ->add('amount')
            ->add('description')
            ->add('isPaid', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
            ])
            ->add('paidAt', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['value' => Date('Y-m-d')],
            ])
            ->add('property', EntityType::class, [
                'class' => Property::class,
                'query_builder' => function (PropertyRepository $propertyRepository) {
                    return $propertyRepository->findAccessibleProperties($this->security->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER], false);
                },
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FinancialEntry::class,
        ]);
    }
}
