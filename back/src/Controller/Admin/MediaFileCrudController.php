<?php

namespace App\Controller\Admin;

use App\Entity\MediaFile;
use App\Service\FileStorageService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\FileType;

#[AdminRoute(path: 'media', name: 'media')]
class MediaFileCrudController extends AbstractCrudController
{
    public function __construct(private readonly FileStorageService $fileStorage) {}

    public static function getEntityFqcn(): string
    {
        return MediaFile::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Média')
            ->setEntityLabelInPlural('Médias')
            ->setPageTitle(Crud::PAGE_INDEX, 'Médiathèque')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un média')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier le média')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['originalName', 'alt', 'mimeType'])
            ->setDefaultRowAction(Crud::PAGE_DETAIL);
    }

    public function configureActions(Actions $actions): Actions
    {
        $download = Action::new('download', 'Télécharger', 'fa fa-download')
            ->linkToUrl(static fn (MediaFile $entity): string => '/backoffice/media/' . $entity->getId() . '/download')
            ->setHtmlAttributes(['target' => '_blank'])
            ->displayIf(static fn (MediaFile $entity): bool => null !== $entity->getId());

        return $actions
            ->add(Crud::PAGE_INDEX, $download)
            ->add(Crud::PAGE_DETAIL, $download)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, 'download', Action::EDIT, Action::DELETE]);
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                ImageField::new('thumbnailPath', 'Aperçu')
                    ->setBasePath('/backoffice/media/thumbnail/'),
                TextField::new('originalName', 'Nom du fichier'),
                TextField::new('mimeType', 'Type'),
                TextField::new('formattedSize', 'Taille'),
                TextField::new('alt', 'Alt'),
                TextField::new('compressionRatio', 'Compression'),
                BooleanField::new('isVisibleOnWebsite', 'Visible sur le site'),
                DateTimeField::new('createdAt', 'Ajouté le')
                    ->setFormat('dd/MM/yyyy HH:mm'),
            ];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                FormField::addColumn(6),
                FormField::addPanel('Aperçu')->setIcon('fa fa-image'),
                ImageField::new('thumbnailPath', '')
                    ->setBasePath('/backoffice/media/thumbnail/')
                    ->setColumns(12),
                TextField::new('alt', 'Texte descriptif')->setColumns(12),
                BooleanField::new('isVisibleOnWebsite', 'Visible sur le site')->setColumns(12),

                FormField::addColumn(6),
                FormField::addPanel('Métadonnées')->setIcon('fa fa-tags'),
                TextField::new('originalName', 'Nom du fichier')->setColumns(6),
                TextField::new('mimeType', 'Type MIME')->setColumns(6),
                TextField::new('formattedSize', 'Taille originale')->setColumns(6),
                TextField::new('compressionRatio', 'Gain de compression')->setColumns(6),
                DateTimeField::new('createdAt', 'Ajouté le')
                    ->setFormat('dd/MM/yyyy HH:mm')
                    ->setColumns(12),
                DateTimeField::new('updatedAt', 'Modifié le')
                    ->setFormat('dd/MM/yyyy HH:mm')
                    ->setColumns(12),
            ];
        }

        if (Crud::PAGE_NEW === $pageName) {
            return [
                FormField::addColumn(8),
                FormField::addPanel('Fichier')->setIcon('fa fa-upload'),
                Field::new('uploadedFile', 'Fichier')
                    ->setFormType(FileType::class)
                    ->setFormTypeOptions(['required' => true])
                    ->setHelp('Images, vidéos, PDF, documents… Taille maximale selon votre configuration PHP.')
                    ->setColumns(12),
                TextField::new('alt', 'Texte descriptif')
                    ->setHelp('Texte alternatif important pour l\'accessibilité.')
                    ->setRequired(false)
                    ->setColumns(12),

                FormField::addColumn(4),
                FormField::addPanel('Visibilité')->setIcon('fa fa-eye'),
                BooleanField::new('isVisibleOnWebsite', 'Visible sur le site')
                    ->setHelp('Indique si ce média doit être affiché sur le site internet.')
                    ->setFormTypeOptions(['data' => true])
                    ->setColumns(12),
            ];
        }

        // PAGE_EDIT
        return [
            FormField::addColumn(8),
            FormField::addPanel('Information')->setIcon('fa fa-info-circle'),
            TextField::new('alt', 'Texte descriptif')
                ->setHelp('Texte alternatif important pour l\'accessibilité.')
                ->setRequired(false)
                ->setColumns(12),

            FormField::addColumn(4),
            FormField::addPanel('Visibilité')->setIcon('fa fa-eye'),
            BooleanField::new('isVisibleOnWebsite', 'Visible sur le site')
                ->setHelp('Indique si ce média doit être affiché sur le site internet.')
                ->setColumns(12),
        ];
    }

    // -------------------------------------------------------------------------
    // Hooks persistEntity / updateEntity / deleteEntity
    // -------------------------------------------------------------------------

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        /** @var MediaFile $entityInstance */
        $uploaded = $entityInstance->getUploadedFile();
        if (!$uploaded) {
            throw new \RuntimeException('Aucun fichier sélectionné.');
        }

        $entityInstance->setCreatedAt(new \DateTimeImmutable());
        $this->fileStorage->store($entityInstance, $uploaded);
        $entityInstance->setUploadedFile(null);

        parent::persistEntity($em, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        /* @var MediaFile $entityInstance */
        $entityInstance->setUpdatedAt(new \DateTimeImmutable());

        parent::updateEntity($em, $entityInstance);
    }

    public function deleteEntity(EntityManagerInterface $em, $entityInstance): void
    {
        /* @var MediaFile $entityInstance */
        $this->fileStorage->delete($entityInstance);

        parent::deleteEntity($em, $entityInstance);
    }
}
