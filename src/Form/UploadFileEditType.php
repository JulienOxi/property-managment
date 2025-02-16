<?php

namespace App\Form;

use App\Entity\UploadFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadFileEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $extraData = $options['extra_data'] ?? [];

        $builder
            ->add('description')
            ->add('file', FileType::class)
            ->add('entityId', HiddenType::class,[
                'data' => $extraData['entityId'],
            ])
            ->add('entityClass', HiddenType::class,[
                'data' => $extraData['entityClass'],
            ])
            ->add('type', HiddenType::class,[
                'data' => $extraData['type'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UploadFile::class,
            'extra_data' => null, // Option personnalisée pour les données supplémentaires
        ]);

        $resolver->setAllowedTypes('extra_data', ['array', 'null']);
    }
}
