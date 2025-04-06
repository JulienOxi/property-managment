<?php

namespace App\Form;

use App\Entity\Bank;
use App\Entity\Mortgage;
use App\Entity\Property;
use App\Enum\MortgageTypeEnum;
use Doctrine\ORM\QueryBuilder;
use App\Repository\BankRepository;
use App\Enum\MortgageBillingPeriodEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



class MortgageType extends AbstractType
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('FromAt', null, [
                'widget' => 'single_text',
                'label' => 'Date de début',
            ])
            ->add('toAt', null, [
                'widget' => 'single_text',
                'label' => 'Date de fin',

            ])
            ->add('billingPeriod', ChoiceType::class, [
                    'choices' => MortgageBillingPeriodEnum::cases(), // Liste des enums
                    'choice_label' => fn(MortgageBillingPeriodEnum $type) => $type->value, // Affichage du label
                    'choice_value' => fn(?MortgageBillingPeriodEnum $type) => $type?->name, // Utilisation du nom de l'enum pour la valeur
                    'label' => 'Fréquence de facturation',
                ])
            ->add('mortgageType', ChoiceType::class, [
                    'choices' => MortgageTypeEnum::cases(), // Liste des enums
                    'choice_label' => fn(MortgageTypeEnum $type) => $type->value, // Affichage du label
                    'choice_value' => fn(?MortgageTypeEnum $type) => $type?->name, // Utilisation du nom de l'enum pour la valeur
                    'label' => 'Type de taux',
                ])
            ->add('rate', null, [
                'label' => 'Taux (%)',
                'attr' => [
                    'placeholder' => 'Taux en pourcent',
                ],
            ])
            ->add('bank', EntityType::class, [
                'query_builder' => function (BankRepository $bankRepository):QueryBuilder {
                    return $bankRepository->createQueryBuilder('b')
                        ->where('b.createdBy = :createdBy')
                        ->setParameter('createdBy', $this->security->getUser());
                },
                'class' => Bank::class,
                'choice_label' => 'name',
                'required' => true,
                'label' => 'Banque',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mortgage::class,
        ]);
    }
}
