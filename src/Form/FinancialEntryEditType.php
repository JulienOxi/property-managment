<?php

namespace App\Form;

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

class FinancialEntryEditType extends AbstractType
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
            ])
            //Ajout du chant UploadFile
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {  
                $data = $event->getData();
                $form = $event->getForm();                
                $this->updateUploadFile($form, $data);
            })
        ;
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
    


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FinancialEntry::class,
        ]);
    }
}
