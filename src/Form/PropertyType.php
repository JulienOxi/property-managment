<?php

namespace App\Form;

use App\Entity\Property;
use App\Enum\MortgageEnum;
use App\Enum\PropertyEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class)
            ->add('type', ChoiceType::class, [
                'choices' => PropertyEnum::cases(), // Liste des enums
                'choice_label' => fn(PropertyEnum $type) => $type->value, // Affichage du label
                'choice_value' => fn(?PropertyEnum $type) => $type?->name, // Utilisation du nom de l'enum pour la valeur
                'placeholder' => 'Sélectionnez un type de propriété', // Optionnel
            ])
            ->add('purchasePrice')
            ->add('purchaseDate', null, [
                'widget' => 'single_text',
            ])
            ->add('mortgageRate')
            ->add('mortgageType', ChoiceType::class, [
                    'choices' => MortgageEnum::cases(), // Liste des enums
                    'choice_label' => fn(MortgageEnum $type) => $type->value, // Affichage du label
                    'choice_value' => fn(?MortgageEnum $type) => $type?->name, // Utilisation du nom de l'enum pour la valeur
                    'placeholder' => 'Type de taux', // Optionnel
                ])
            ->add('mortgageEndDate', null, [
                'widget' => 'single_text',
            ])
            ->add('EWID')
            ->add('EGID')
            ->add('address', AddressWoPhoneType::class, [])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
