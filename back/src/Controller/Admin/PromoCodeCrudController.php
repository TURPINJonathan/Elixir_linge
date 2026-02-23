<?php

namespace App\Controller\Admin;

use App\Entity\PromoCode;
use App\Enum\PromoCodeDiscountType;
use App\Enum\PromoCodeType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints as Assert;

class PromoCodeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PromoCode::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Code promo')
            ->setEntityLabelInPlural('Codes promo')
            ->setPageTitle(Crud::PAGE_INDEX, 'Codes promo')
            ->setDefaultSort(['start_at' => 'DESC'])
            ->setSearchFields(['name', 'description', 'type', 'discountType', 'amount'])
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
                TextField::new('name', 'Nom'),
                NumberField::new('amount', 'Valeur'),
                ChoiceField::new('discountType', 'Mode de réduction')
                    ->setChoices([
                        PromoCodeDiscountType::PERCENTAGE->label() => PromoCodeDiscountType::PERCENTAGE,
                        PromoCodeDiscountType::FIXED->label()      => PromoCodeDiscountType::FIXED,
                    ]),
                DateTimeField::new('startAt', 'Début')->setFormat('dd/MM/yyyy'),
                DateTimeField::new('endAt', 'Fin')->setFormat('dd/MM/yyyy'),
            ];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                FormField::addColumn(6),
                FormField::addPanel('Informations')->setIcon('fa fa-ticket'),
                TextField::new('name', 'Nom')->setColumns(12),
                TextareaField::new('description', 'Description')->renderAsHtml()->setColumns(12),

                FormField::addColumn(6),
                FormField::addPanel('Réduction')->setIcon('fa fa-percent'),
                ChoiceField::new('discountType', 'Mode de réduction')
                    ->setChoices([
                        PromoCodeDiscountType::PERCENTAGE->label() => PromoCodeDiscountType::PERCENTAGE,
                        PromoCodeDiscountType::FIXED->label()      => PromoCodeDiscountType::FIXED,
                    ])
                    ->setColumns(12),
                NumberField::new('amount', 'Valeur')->setColumns(12),

                FormField::addColumn(12),
                FormField::addPanel('Période de validité')->setIcon('fa fa-calendar'),
                DateTimeField::new('startAt', 'Début')->setColumns(6),
                DateTimeField::new('endAt', 'Fin')->setColumns(6),
            ];
        }

        // PAGE_EDIT / PAGE_NEW
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex()->hideOnDetail(),

            FormField::addColumn(12),
            FormField::addPanel('Informations')->setIcon('fa fa-ticket'),
            TextField::new('name', 'Nom')
                ->setHelp('Ex: "Offre de lancement"')
                ->setColumns(12),
            ChoiceField::new('type', 'Type')
                ->setChoices([
                    PromoCodeType::CUSTOM->label()  => PromoCodeType::CUSTOM,
                    PromoCodeType::GENERAL->label() => PromoCodeType::GENERAL,
                    PromoCodeType::SERVICE->label() => PromoCodeType::SERVICE,
                ])
                ->hideOnIndex()
                ->hideOnDetail()
                ->hideOnForm(),
            TextEditorField::new('description', 'Description')
                ->setHelp('Ex: "Ressortez vos t-shirts préférés pour l\'été avec ce code promo !"')
                ->setFormTypeOption('attr', ['placeholder' => 'Décrivez l\'offre...', 'rows' => 3])
                ->setColumns(12),

            FormField::addColumn(6),
            FormField::addPanel('Réduction')->setIcon('fa fa-percent'),
            NumberField::new('amount', 'Valeur')
            ->setHelp('Ex: 10 ou 15.5')
                ->setNumDecimals(2)
                ->setFormTypeOption('constraints', [
                    new Assert\PositiveOrZero(),
                    new Assert\Regex(
                        pattern: '/^\d+(?:[\.,]\d{1,2})?$/',
                        message: 'Valeur invalide.',
                    ),
                ])
                ->setColumns(6),
            ChoiceField::new('discountType', 'Mode de réduction')
                ->setChoices([
                    PromoCodeDiscountType::PERCENTAGE->label() => PromoCodeDiscountType::PERCENTAGE,
                    PromoCodeDiscountType::FIXED->label()      => PromoCodeDiscountType::FIXED,
                ])
                ->setColumns(6),

            FormField::addColumn(6),
            FormField::addPanel('Période de validité')->setIcon('fa fa-calendar'),
            DateTimeField::new('startAt', 'Début')
                ->setHelp('Date et heure de début de validité')
                ->setColumns(6),
            DateTimeField::new('endAt', 'Fin')
                ->setHelp('Date et heure de fin de validité')
                ->setColumns(6),
        ];
    }
}
