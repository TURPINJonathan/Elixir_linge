<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RateRepository::class)]
#[ApiResource]
class Rate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $size = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::JSON)]
    private int|string|null $rate = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private int|string|null $reduced_rate = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private int|string|null $rate_after_tax_reduction = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updated_at = null;

    private ?bool $isOnQuotation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRate(): int|string|null
    {
        return $this->rate;
    }

    public function setRate(int|string|null $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getReducedRate(): int|string|null
    {
        return $this->reduced_rate;
    }

    public function setReducedRate(int|string|null $reduced_rate): static
    {
        $this->reduced_rate = $reduced_rate;

        return $this;
    }

    public function getRateAfterTaxReduction(): int|string|null
    {
        return $this->rate_after_tax_reduction;
    }

    public function setRateAfterTaxReduction(int|string|null $rate_after_tax_reduction): static
    {
        $this->rate_after_tax_reduction = $rate_after_tax_reduction;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTime $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function isOnQuotation(): bool
    {
        if (null !== $this->isOnQuotation) {
            return $this->isOnQuotation;
        }

        $values = [$this->rate, $this->reduced_rate, $this->rate_after_tax_reduction];
        foreach ($values as $value) {
            if ('on_quotation' === $value || 'on quotation' === $value) {
                return true;
            }
        }

        return false;
    }

    public function setIsOnQuotation(?bool $isOnQuotation): static
    {
        $this->isOnQuotation = $isOnQuotation;

        return $this;
    }
}
