<?php

namespace App\Controller\Admin;

use App\Controller\ResetPasswordController;
use App\Entity\Distributor;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_NEW, "Nouvel utilisateur")
            ->setEntityLabelInSingular('utilisateur')
            ->setEntityLabelInPlural('Utilisateurs');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email', 'Email'),
            TextField::new('name', 'Nom'),
            TextField::new('firstname', 'Prénom'),
            ChoiceField::new('role','Rôle')
                ->setChoices([
                    "Administrateur"=>"ROLE_ADMIN",
                    "Back-office"=>"ROLE_BACKOFFICE",
                    "Commercial"=>"ROLE_SALESMAN"
                    ]),
            TextField::new('matricule','Matricule')
                ->onlyOnDetail()
                ->formatValue(function($value){
                    return $value?:'Aucun matricule disponible';
                }),
            AssociationField::new('distributor', 'Distributeur')
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function(Action $action){
                return $action->setLabel("Créer un nouvel utilisateur");
                })
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER, function(Action $action){
                return $action->setLabel("Créer et ajouter un nouvel utilisateur");
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action){
                return $action->displayIf(function($entity){
                    return $entity->getRole() == "ROLE_BACKOFFICE" || $entity->getRole() == "ROLE_ADMIN";
                });
            });
        return parent::configureActions($actions); // TODO: Change the autogenerated stub
    }
}
