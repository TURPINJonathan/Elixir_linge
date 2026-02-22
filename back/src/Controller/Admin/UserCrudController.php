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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setSearchFields(['email', 'firstname', 'lastname'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnDetail()->hideOnForm()->hideOnIndex();

        yield EmailField::new('email');

        yield TextField::new('firstname', 'Prénom');
        yield TextField::new('lastname', 'Nom');

        $choices = [
            'User' => 'ROLE_USER',
            'Admin' => 'ROLE_ADMIN',
        ];

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $choices['Super Admin'] = 'ROLE_SUPER_ADMIN';
        }

        yield ChoiceField::new('roles')
            ->setChoices($choices)
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
            $this->assertCanManageUser($entityInstance);

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
            $this->assertCanManageUser($entityInstance);

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

    private function assertCanManageUser(User $user): void
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Vous n\'avez pas les droits pour créer/modifier des utilisateurs.');
        }

        $roles = array_values(array_filter(array_unique($user->getRoles()), static fn (string $role): bool => in_array($role, [
            'ROLE_USER',
            'ROLE_ADMIN',
            'ROLE_SUPER_ADMIN',
        ], true)));

        if (in_array('ROLE_SUPER_ADMIN', $roles, true) && !$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('Seul un super admin peut attribuer ROLE_SUPER_ADMIN.');
        }

        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            foreach ($roles as $role) {
                if (!in_array($role, ['ROLE_USER', 'ROLE_ADMIN'], true)) {
                    throw new AccessDeniedException('Un admin ne peut attribuer que ROLE_ADMIN ou ROLE_USER.');
                }
            }
        }

        $user->setRoles($roles);
    }
}