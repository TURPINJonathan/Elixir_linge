<?php

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Enum\CustomerTitle;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

#[AdminRoute(path: 'customers', name: 'customers')]
class CustomerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Customer::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Client')
            ->setEntityLabelInPlural('Clients')
            ->setPageTitle(Crud::PAGE_INDEX, 'Clients')
            ->setDefaultSort(['created_at' => 'DESC'])
            ->setSearchFields(['firstname', 'lastname', 'email', 'phone_number', 'city'])
            ->setDefaultRowAction(Action::DETAIL);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                IdField::new('id')->hideOnForm()->hideOnIndex()->hideOnDetail(),
                ChoiceField::new('title', 'Civilité')
                    ->setChoices([
                        CustomerTitle::MR->label()    => CustomerTitle::MR->value,
                        CustomerTitle::MRS->label()   => CustomerTitle::MRS->value,
                        CustomerTitle::OTHER->label() => CustomerTitle::OTHER->value,
                    ]),
                TextField::new('lastname', 'Nom'),
                TextField::new('firstname', 'Prénom'),
                TextField::new('city', 'Ville'),
                EmailField::new('email', 'Email'),
                TelephoneField::new('phoneNumber', 'Téléphone'),
                AssociationField::new('company', 'Entreprise'),
            ];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                FormField::addColumn(6),
                FormField::addPanel('Informations personnelles')->setIcon('fa fa-user'),
                ChoiceField::new('title', 'Civilité')
                    ->setChoices([
                        CustomerTitle::MR->label()    => CustomerTitle::MR,
                        CustomerTitle::MRS->label()   => CustomerTitle::MRS,
                        CustomerTitle::OTHER->label() => CustomerTitle::OTHER,
                    ])
                    ->setColumns(12),
                TextField::new('lastname', 'Nom')->setColumns(6),
                TextField::new('firstname', 'Prénom')->setColumns(6),

                FormField::addColumn(6),
                FormField::addPanel('Coordonnées')->setIcon('fa fa-map-marker'),
                TextField::new('address', 'Adresse')->setColumns(12),
                TextField::new('postalCode', 'Code postal')->setColumns(3),
                TextField::new('city', 'Ville')->setColumns(9),

                FormField::addColumn(6),
                FormField::addPanel('Contact')->setIcon('fa fa-phone'),
                TelephoneField::new('phoneNumber', 'Téléphone')->setColumns(6),
                EmailField::new('email', 'Email')->setColumns(6),

                FormField::addColumn(6),
                FormField::addPanel('Entreprise')->setIcon('fa fa-building'),
                AssociationField::new('company', 'Entreprise')->setColumns(12),

                FormField::addColumn(12),
                FormField::addPanel('Informations complémentaires')->setIcon('fa fa-plus'),
                TextareaField::new('privateNote', 'Note privée')->renderAsHtml()->setColumns(12),
            ];
        }

        // PAGE_EDIT / PAGE_NEW
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex()->hideOnDetail(),

            FormField::addColumn(6),
            FormField::addPanel('Informations personnelles')->setIcon('fa fa-user'),
            ChoiceField::new('title', 'Civilité')
                ->setChoices([
                    CustomerTitle::MR->label()    => CustomerTitle::MR,
                    CustomerTitle::MRS->label()   => CustomerTitle::MRS,
                    CustomerTitle::OTHER->label() => CustomerTitle::OTHER,
                ])
                ->setColumns(3),
            TextField::new('lastname', 'Nom')
                ->setFormTypeOption('attr', ['placeholder' => 'Turpi'])
                ->setColumns(5),
            TextField::new('firstname', 'Prénom')
                ->setFormTypeOption('attr', ['placeholder' => 'Nou'])
                ->setColumns(4),

            FormField::addColumn(6),
            FormField::addPanel('Entreprise')->setIcon('fa fa-building'),
            AssociationField::new('company', 'Entreprise')
                ->setColumns(12),

            FormField::addColumn(6),
            FormField::addPanel('Coordonnées')->setIcon('fa fa-map-marker'),
            TextField::new('address', 'Adresse')
                ->setFormTypeOption('attr', ['placeholder' => '10 rue du beaugoss'])
                ->setColumns(12),
            TextField::new('postalCode', 'Code postal')
                ->setFormTypeOption('attr', ['placeholder' => '14000'])
                ->setColumns(3),
            TextField::new('city', 'Ville')
                ->setFormTypeOption('attr', ['placeholder' => 'Mont Olympe'])
                ->setColumns(9),

            FormField::addColumn(6),
            FormField::addPanel('Contact')->setIcon('fa fa-phone'),
            TelephoneField::new('phoneNumber', 'Téléphone')
                ->setFormTypeOption('attr', ['placeholder' => '06 12 34 56 78'])
                ->setColumns(12),
            EmailField::new('email', 'Email')
                ->setFormTypeOption('attr', ['placeholder' => 'turpinou@bg.com'])
                ->setColumns(12),

            FormField::addColumn(12),

            FormField::addPanel('Informations complémentaires   ')->setIcon('fa fa-plus'),
            TextEditorField::new('privateNote', 'Note privée')
                ->setFormTypeOption('attr', ['placeholder' => 'Notes internes sur le client...', 'rows' => 4])
                ->setHelp('Cette note ne sera jamais visible par le client')
                ->setColumns(12),

            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Modifié le')->hideOnForm(),
        ];
    }
}
