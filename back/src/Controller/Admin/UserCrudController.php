<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield EmailField::new('email');

        yield TextField::new('firstname', 'Prénom');
        yield TextField::new('lastname', 'Nom');

        yield ChoiceField::new('roles')
            ->setChoices([
                'User' => 'ROLE_USER',
                'Admin' => 'ROLE_ADMIN',
            ])
            ->allowMultipleChoices();

        // Mot de passe :
        // - obligatoire en création
        // - optionnel en édition (si vide => ne change pas le password)
        yield TextField::new('plainPassword', 'Mot de passe')
            ->setFormType(PasswordType::class)
            ->onlyOnForms()
            ->setRequired($pageName === Crud::PAGE_NEW);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            // created_at NOT NULL
            if ($entityInstance->getCreatedAt() === null) {
                $entityInstance->setCreatedAt(new \DateTimeImmutable());
            }

            // password NOT NULL
            $this->hashPasswordOrFailOnCreate($entityInstance);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            // En édition: on ne change le password que si l’admin a saisi un nouveau mot de passe
            if ($entityInstance->getPlainPassword()) {
                $entityInstance->setPassword(
                    $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPlainPassword())
                );
                $entityInstance->setPlainPassword(null);
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function hashPasswordOrFailOnCreate(User $user): void
    {
        $plain = $user->getPlainPassword();
        if (!$plain) {
            // Si tu arrives ici, c’est que le formulaire a laissé passer un mot de passe vide.
            // On bloque proprement plutôt que d’insérer NULL.
            throw new \RuntimeException('Mot de passe obligatoire à la création.');
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $plain));
        $user->setPlainPassword(null);
    }
}