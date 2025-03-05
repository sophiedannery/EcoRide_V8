<?php

namespace App\Controller\Admin;

use App\Entity\Voiture;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class VoitureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Voiture::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('plaqueImmatriculation', 'Plaque d\'immatriculation');
        yield DateField::new('datePremiereImmatriculation', 'Date de la première immatriculation');
        yield TextField::new('marque', 'Marque');
        yield TextField::new('modele', 'Modèle');
        yield TextField::new('couleur', 'Couleur');
        yield IntegerField::new('nbPlace', 'Nombre de places');
        yield ChoiceField::new('energie', 'Energie')
            ->setChoices([
                'Essence' => 'essence',
                'Diesel' => 'diesel',
                'Hybride' => 'hybride',
                'Electrique' => 'electrique',
                'GPL' => 'gpl',
                'Autre' => 'autre',
            ])
            ->renderExpanded();
        yield AssociationField::new('user', 'Utilisateur')
            ->autocomplete();
    }
}
