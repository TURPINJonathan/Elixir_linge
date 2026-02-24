<?php

namespace App\Controller\Admin;

use App\Entity\Rate;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rate::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Tarif')
            ->setEntityLabelInPlural('Tarifs')
            ->setPageTitle(Crud::PAGE_INDEX, 'Tarifs')
            ->setDefaultSort(['created_at' => 'DESC'])
            ->setSearchFields(['size', 'description'])
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
                TextField::new('size', 'Taille'),
                TextField::new('description', 'Description')->renderAsHtml()->setMaxLength(80),
                TextField::new('rate', 'Tarif')->formatValue(static fn ($value) => self::formatRateValue($value)),
                // TextField::new('reducedRate', 'Tarif réduit')->formatValue(static fn ($value) => self::formatRateValue($value)),
                TextField::new('rateAfterTaxReduction', 'Tarif après déduction')->formatValue(static fn ($value) => self::formatRateValue($value)),
            ];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                FormField::addColumn(6),
                FormField::addPanel('Informations')->setIcon('fa fa-tag'),
                TextField::new('size', 'Taille')->setColumns(12),
                BooleanField::new('isOnQuotation', 'Sur devis')->formatValue(static fn ($value) => $value ? 'Oui' : 'Non')->setColumns(12),
                TextField::new('description', 'Description')->renderAsHtml()->setColumns(12),

                FormField::addColumn(6),
                FormField::addPanel('Montants')->setIcon('fa fa-coins'),
                TextField::new('rate', 'Tarif')->formatValue(static fn ($value) => self::formatRateValue($value))->setColumns(12),
                // TextField::new('reducedRate', 'Tarif réduit')->formatValue(static fn ($value) => self::formatRateValue($value))->setColumns(12),
                TextField::new('rateAfterTaxReduction', 'Tarif après déduction')->formatValue(static fn ($value) => self::formatRateValue($value))->setColumns(12),
            ];
        }

        // PAGE_EDIT / PAGE_NEW
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex()->hideOnDetail(),

            FormField::addColumn(6)->addCssClass('js-informations-column'),
            FormField::addPanel('Informations')->setIcon('fa fa-tag'),
            TextField::new('size', 'Taille')
                ->setFormTypeOption('attr', ['placeholder' => 'S, M, L...'])
                ->setColumns(12),
            BooleanField::new('isOnQuotation', 'Sur devis ?')
                ->setColumns(12),
            TextEditorField::new('description', 'Description')
                ->setFormTypeOption('attr', ['placeholder' => 'Description du tarif...'])
                ->setHelp($this->renderQuotationToggleScript())
                ->setColumns(12),

            FormField::addColumn(6)->addCssClass('js-montants-column'),
            FormField::addPanel('Montants')->setIcon('fa fa-coins')->addCssClass('js-montants-panel'),
            IntegerField::new('rate', 'Tarif plein')
                ->setFormTypeOption('row_attr', ['class' => 'js-rate-fields'])
                ->setColumns(12),
            // IntegerField::new('reducedRate', 'Tarif réduit')
            //     ->setHelp('Optionnel')
            //     ->setFormTypeOption('row_attr', ['class' => 'js-rate-fields'])
            //     ->setColumns(12),
            IntegerField::new('rateAfterTaxReduction', 'Tarif après déduction')
                ->setFormTypeOption('row_attr', ['class' => 'js-rate-fields'])
                ->setColumns(12),

            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Modifié le')->hideOnForm(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Rate) {
            $this->applyQuotationMode($entityInstance);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Rate) {
            $this->applyQuotationMode($entityInstance);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function applyQuotationMode(Rate $rate): void
    {
        if ($rate->isOnQuotation()) {
            $rate->setRate('on_quotation');
            $rate->setReducedRate('on_quotation');
            $rate->setRateAfterTaxReduction('on_quotation');

            return;
        }

        $rate->setRate(self::normalizeIntegerOrNull($rate->getRate()));
        $rate->setReducedRate(self::normalizeIntegerOrNull($rate->getReducedRate()));
        $rate->setRateAfterTaxReduction(self::normalizeIntegerOrNull($rate->getRateAfterTaxReduction()));

        if (null === $rate->getRate()) {
            throw new \RuntimeException('Le champ "Tarif" est obligatoire quand "Sur devis" est désactivé.');
        }
    }

    private static function normalizeIntegerOrNull(mixed $value): ?int
    {
        if (null === $value || '' === $value) {
            return null;
        }

        return (int) $value;
    }

    private static function formatRateValue(mixed $value): string
    {
        if (null === $value || '' === $value) {
            return '-';
        }

        if ('on_quotation' === $value || 'on quotation' === $value) {
            return 'Sur devis';
        }

        return (string) $value;
    }

    private function renderQuotationToggleScript(): string
    {
        return <<<'HTML'
            <script>
            document.addEventListener('DOMContentLoaded', function () {
                const checkbox = document.querySelector('input[name$="[isOnQuotation]"]');
                if (!checkbox) {
                    return;
                }

                const priceRows = document.querySelectorAll('.js-rate-fields');
                const montantsPanel = document.querySelector('.js-montants-panel');
                const montantsColumn = document.querySelector('.js-montants-column');
                const informationsColumn = document.querySelector('.js-informations-column');

                const syncRows = function () {
                    const hidePrices = checkbox.checked;

                    if (montantsColumn) {
                        montantsColumn.style.display = hidePrices ? 'none' : '';
                    }

                    if (montantsPanel) {
                        montantsPanel.style.display = hidePrices ? 'none' : '';
                    }

                    if (informationsColumn) {
                        if (hidePrices) {
                            informationsColumn.style.flex = '0 0 100%';
                            informationsColumn.style.maxWidth = '100%';
                        } else {
                            informationsColumn.style.flex = '';
                            informationsColumn.style.maxWidth = '';
                        }
                    }

                    priceRows.forEach(function (row) {
                        row.style.display = hidePrices ? 'none' : '';

                        const input = row.querySelector('input, select, textarea');
                        if (input) {
                            input.disabled = hidePrices;
                        }
                    });
                };

                checkbox.addEventListener('change', syncRows);
                syncRows();
            });
            </script>
            HTML;
    }
}
