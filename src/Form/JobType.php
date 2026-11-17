<?php

namespace App\Form;

use App\Entity\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type')
            ->add('company')
            ->add('discription')
            ->add('expiresAt')
            ->add('email')

            ->add('image', EntityType::class, [
                'class' => Image::class,
                'choice_label' => 'alt',
                'placeholder' => 'Choisir une image',
                'required' => false,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
