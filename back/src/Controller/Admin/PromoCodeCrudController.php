<?php

namespace App\Controller\Admin;

use App\Entity\PromoCode;
use App\Enum\PromoCodeDiscountType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
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
            ->setSearchFields(['name', 'description', 'type', 'discountType', 'amount']);
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                IdField::new('id')->hideOnForm()->hideOnIndex()->hideOnDetail(),
                TextField::new('name', 'Nom'),
                ChoiceField::new('discountType', 'Mode de réduction')
                    ->setChoices([
                        PromoCodeDiscountType::PERCENTAGE->label() => PromoCodeDiscountType::PERCENTAGE,
                        PromoCodeDiscountType::FIXED->label()      => PromoCodeDiscountType::FIXED,
                    ]),
                NumberField::new('amount', 'Valeur'),
                DateTimeField::new('startAt', 'Début'),
                DateTimeField::new('endAt', 'Fin'),
            ];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                FormField::addPanel('Informations')->setIcon('fa fa-ticket'),
                TextField::new('name', 'Nom')->setColumns(12),
                ChoiceField::new('discountType', 'Mode de réduction')
                    ->setChoices([
                        PromoCodeDiscountType::PERCENTAGE->label() => PromoCodeDiscountType::PERCENTAGE,
                        PromoCodeDiscountType::FIXED->label()      => PromoCodeDiscountType::FIXED,
                    ])
                    ->setColumns(12),
                TextareaField::new('description', 'Détails')->setColumns(12),

                FormField::addPanel('Valeur et période')->setIcon('fa fa-calendar'),
                NumberField::new('amount', 'Valeur')->setColumns(4),
                DateTimeField::new('startAt', 'Début')->setColumns(4),
                DateTimeField::new('endAt', 'Fin')->setColumns(4),
            ];
        }

        return [
            IdField::new('id')->hideOnForm()->hideOnIndex()->hideOnDetail(),
            FormField::addPanel('Informations')->setIcon('fa fa-ticket'),
            TextField::new('name', 'Nom')->setColumns(12),
            TextareaField::new('description', 'Détails')
                ->setHelp('Ex: "Ressortez vos t shirt préférés pour l\'été avec ce code promo !"')
                ->setColumns(12),

            FormField::addPanel('Valeur et période')->setIcon('fa fa-calendar'),
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
                ->setColumns(4),
            ChoiceField::new('discountType', 'Mode de réduction')
                ->setChoices([
                    PromoCodeDiscountType::PERCENTAGE->label() => PromoCodeDiscountType::PERCENTAGE,
                    PromoCodeDiscountType::FIXED->label()      => PromoCodeDiscountType::FIXED,
                ])
                ->setColumns(4),
            DateTimeField::new('startAt', 'Début')->setColumns(2),
            DateTimeField::new('endAt', 'Fin')->setColumns(2),
        ];
    }
}
