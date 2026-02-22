<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\PromoCodeDiscountType;
use App\Enum\PromoCodeType;
use App\Repository\PromoCodeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PromoCodeRepository::class)]
#[ApiResource]
class PromoCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero]
    #[Assert\Regex(
        pattern: '/^\d+(?:[\.,]\d{1,2})?$/',
        message: 'Valeur invalide.',
    )]
    private ?string $amount = null;

    #[ORM\Column]
    private ?\DateTime $start_at = null;

    #[ORM\Column]
    private ?\DateTime $end_at = null;

    #[ORM\Column(enumType: PromoCodeType::class)]
    private ?PromoCodeType $type = PromoCodeType::CUSTOM;

    #[ORM\Column(enumType: PromoCodeDiscountType::class)]
    private ?PromoCodeDiscountType $discountType = PromoCodeDiscountType::PERCENTAGE;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStartAt(): ?\DateTime
    {
        return $this->start_at;
    }

    public function setStartAt(\DateTime $start_at): static
    {
        $this->start_at = $start_at;

        return $this;
    }

    public function getEndAt(): ?\DateTime
    {
        return $this->end_at;
    }

    public function setEndAt(\DateTime $end_at): static
    {
        $this->end_at = $end_at;

        return $this;
    }

    public function getType(): ?PromoCodeType
    {
        return $this->type;
    }

    public function setType(PromoCodeType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDiscountType(): ?PromoCodeDiscountType
    {
        return $this->discountType;
    }

    public function setDiscountType(PromoCodeDiscountType $discountType): static
    {
        $this->discountType = $discountType;

        return $this;
    }
}
