<?php

namespace App\Form;

use App\Entity\Bank;
use App\Entity\Property;
use App\Enum\MortgageEnum;
use App\Enum\PropertyEnum;
use Symfony\Component\Form\AbstractType;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class PropertyType extends AbstractType
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    
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
            ->add('purchasePrice', NumberType::class, [
                'required' => true
            ])
            ->add('purchaseDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('mortgages', CollectionType::class, [
                'entry_type' => MortgageType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'attr' => [
                    'data-controller' => 'form-collection',
                    'data-form-collection-btnadd-value' => 'Ajouter une hypothèque',
                ]
            ])
            ->add('EWID')
            ->add('EGID')
            ->add('address', AddressWoPhoneType::class, [])
            ->add('ownerChargesDepositAmount', null, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
