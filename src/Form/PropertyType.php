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
                'choices' => array_combine(
                    array_map(fn(PropertyEnum $type) => $type->value, PropertyEnum::cases()), // Labels
                    PropertyEnum::cases() // Values
            )])
            ->add('purchasePrice')
            ->add('purchaseDate', null, [
                'widget' => 'single_text',
            ])
            ->add('mortgageRate')
            ->add('mortgageType', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn(MortgageEnum $type) => $type->value, MortgageEnum::cases()), // Labels
                    MortgageEnum::cases() // Values
            )])
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
