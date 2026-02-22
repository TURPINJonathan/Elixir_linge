<?php

namespace App\EventListener;

use App\Entity\Company;
use App\Service\GeocodingService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: Company::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Company::class)]
class CompanyTimestampListener
{
    public function __construct(
        private readonly GeocodingService $geocodingService,
    ) {}

    public function prePersist(Company $company): void
    {
        if (null === $company->getCreatedAt()) {
            $company->setCreatedAt(new \DateTimeImmutable());
        }

        $this->updateGeoCoordinates($company);
    }

    public function preUpdate(Company $company): void
    {
        $company->setUpdatedAt(new \DateTime());
        $this->updateGeoCoordinates($company);
    }

    private function updateGeoCoordinates(Company $company): void
    {
        // Géocodage automatique si address et city sont renseignés
        if ($company->getAddress() && $company->getCity()) {
            $fullAddress = implode(' ', array_filter([
                $company->getAddress(),
                $company->getPostalCode(),
                $company->getCity(),
            ]));

            $coords = $this->geocodingService->geocode($fullAddress);

            if (null !== $coords) {
                $company->setLatitude($coords['latitude']);
                $company->setLongitude($coords['longitude']);
            }
        }
    }
}
