<?php

namespace App\Form;

use App\Entity\Covoiturage;
use App\Entity\Voiture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CovoiturageFormType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        $builder
            ->add('depart', TextType::class, [
                'label' => 'Lieu de départ',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('arrivee', TextType::class, [
                'label' => 'Lieu d\'arrivée',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('date_heure_depart', DateTimeType::class, [
                'label' => 'Date et heure de départ',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('date_heure_arrivee', DateTimeType::class, [
                'label' => 'Date et heure d\'arrivée',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('prix_personne', IntegerType::class, [
                'label' => 'Prix par personne (dont 2 crédits de frais)',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('is_ecologique', CheckboxType::class, [
                'label' => 'Trajet écologique',
                'required' => false,
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('voiture', EntityType::class, [
                'class' => Voiture::class,
                'choices' => ($user instanceof \App\Entity\User) ? $user->getVoitures()->toArray() : [],
                'choice_label' => function (Voiture $voiture) {
                    return $voiture->getMarque() . ' ' . $voiture->getModele();
                },
                'label' => 'Voiture utilisée',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer le trajet',
                'attr' => ['class' => 'btn btn-primary form-control mb-3 w-100']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Covoiturage::class,
        ]);
    }
}
