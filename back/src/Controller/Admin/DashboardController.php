<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AdminDashboard(routePath: '/backoffice', routeName: 'back_office')]
class DashboardController extends AbstractDashboardController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function persistEntity($entityInstance): void
{
    if ($entityInstance instanceof User) {
        $this->hashPasswordIfProvided($entityInstance);
        if ($entityInstance->getCreatedAt() === null) {
            $entityInstance->setCreatedAt(new \DateTimeImmutable());
        }
    }

    parent::persistEntity($entityInstance);
}

public function updateEntity($entityInstance): void
{
    if ($entityInstance instanceof User) {
        $this->hashPasswordIfProvided($entityInstance);
    }

    parent::updateEntity($entityInstance);
}

private function hashPasswordIfProvided(User $user): void
{
    $plain = $user->getPlainPassword();
    if (!$plain) {
        return;
    }

    $user->setPassword($this->passwordHasher->hashPassword($user, $plain));
    $user->setPlainPassword(null); // on efface pour éviter de le garder en mémoire
}

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Elixir Linge');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);
    }
}
