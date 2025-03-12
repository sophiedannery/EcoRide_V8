<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('pseudo', 'Pseudo');
        yield EmailField::new('email', 'Email');
        yield TextField::new('password', 'Mot de passe')->onlyOnForms();
        yield ChoiceField::new('type', 'Statut')
            ->setChoices([
                'Chauffeur' => 'chauffeur',
                'Passager' => 'passager',
                'Passager et Chauffeur' => 'passager_chauffeur'
            ])
            ->renderExpanded();
        yield IntegerField::new('credit', 'Crédits')->hideOnForm();
        yield ChoiceField::new('roles', 'Rôles')
            ->setChoices([
                'Admin' => 'ROLE_ADMIN',
                'User' => 'ROLE_USER'
            ])
            ->allowMultipleChoices()
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT);
    }
}
