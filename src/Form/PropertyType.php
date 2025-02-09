<?php

namespace App\Form;

use App\Entity\Bank;
use App\Entity\Property;
use App\Enum\MortgageEnum;
use App\Enum\PropertyEnum;
use Doctrine\ORM\QueryBuilder;
use App\Repository\BankRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
            ->add('purchaseDate', null, [
                'widget' => 'single_text',
            ])
            ->add('mortgageRate', null, [
                'required' => true
            ])
            ->add('mortgageType', ChoiceType::class, [
                    'choices' => MortgageEnum::cases(), // Liste des enums
                    'choice_label' => fn(MortgageEnum $type) => $type->value, // Affichage du label
                    'choice_value' => fn(?MortgageEnum $type) => $type?->name, // Utilisation du nom de l'enum pour la valeur
                    'placeholder' => 'Type de taux',
                    'required' => true
                ])
            ->add('mortgageEndDate', null, [
                'widget' => 'single_text',
                'required' => true
            ])
            ->add('mortgageRate2')
            ->add('mortgageType2', ChoiceType::class, [
                    'choices' => MortgageEnum::cases(), // Liste des enums
                    'choice_label' => fn(MortgageEnum $type2) => $type2->value, // Affichage du label
                    'choice_value' => fn(?MortgageEnum $type2) => $type2?->name, // Utilisation du nom de l'enum pour la valeur
                    'placeholder' => 'Type de taux', // Optionnel
                    'required' => false
                ])
            ->add('mortgageEndDate2', null, [
                'widget' => 'single_text',
            ])
            ->add('EWID')
            ->add('EGID')
            ->add('address', AddressWoPhoneType::class, [])
            ->add('bank', EntityType::class, [
                'query_builder' => function (BankRepository $bankRepository):QueryBuilder {
                    return $bankRepository->createQueryBuilder('b')
                        ->where('b.createdBy = :createdBy')
                        ->setParameter('createdBy', $this->security->getUser());
                },
                'class' => Bank::class,
                'choice_label' => 'name',
                'required' => true
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
