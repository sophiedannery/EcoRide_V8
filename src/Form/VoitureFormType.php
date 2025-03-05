<?php

namespace App\Form;

use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoitureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plaque_immatriculation', TextType::class, [
                'label' => 'Plaque d\'immatriculation',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('date_premiere_immatriculation', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de première immatriculation',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('marque', TextType::class, [
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('modele', TextType::class, [
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('couleur', TextType::class, [
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('energie', ChoiceType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'choices' => [
                    'Essence' => 'essence',
                    'Diesel' => 'diesel',
                    'Electrique' => 'electrique',
                    'Hybride' => 'hybride',
                    'GPL' => 'gpl',
                ],
            ])
            ->add('nb_place', IntegerType::class, [
                'label' => 'Nombre de place disponible',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Ajouter la voiture',
                'attr' => ['class' => 'btn btn-primary mb-3 w-100']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
