<?php

namespace App\EventListener;

use App\Entity\Rate;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: Rate::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Rate::class)]
class RateTimestampListener
{
    public function prePersist(Rate $rate): void
    {
        if (null === $rate->getCreatedAt()) {
            $rate->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function preUpdate(Rate $rate): void
    {
        $rate->setUpdatedAt(new \DateTime());
    }
}
