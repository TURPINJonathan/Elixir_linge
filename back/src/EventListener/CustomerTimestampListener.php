<?php

namespace App\EventListener;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: Customer::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Customer::class)]
class CustomerTimestampListener
{
    public function prePersist(Customer $customer): void
    {
        if (null === $customer->getCreatedAt()) {
            $customer->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function preUpdate(Customer $customer): void
    {
        $customer->setUpdatedAt(new \DateTime());
    }
}
