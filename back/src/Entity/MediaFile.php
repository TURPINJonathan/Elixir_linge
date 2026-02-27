<?php

namespace App\Entity;

use App\Repository\MediaFileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: MediaFileRepository::class)]
class MediaFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $originalName;

    #[ORM\Column(length: 100)]
    private string $mimeType;

    /** Taille originale en octets */
    #[ORM\Column]
    private int $size = 0;

    /** Taille stockée sur disque en octets */
    #[ORM\Column]
    private int $storedSize = 0;

    /** Texte alternatif pour l'accessibilité front-end */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $alt = null;

    /** Nom du fichier dans var/uploads/media/ */
    #[ORM\Column(length: 64)]
    private string $storedFilename;

    /** True si le fichier a été compressé avec gzcompress */
    #[ORM\Column]
    private bool $isCompressed = false;

    /** True si une vignette a été générée dans var/uploads/thumbnails/ */
    #[ORM\Column]
    private bool $hasThumbnail = false;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /** Champ transient (non persisté) : fichier uploadé via le formulaire */
    private ?UploadedFile $uploadedFile = null;

    #[ORM\Column]
    private ?bool $isVisibleOnWebsite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): static
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getStoredSize(): int
    {
        return $this->storedSize;
    }

    public function setStoredSize(int $storedSize): static
    {
        $this->storedSize = $storedSize;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): static
    {
        $this->alt = $alt;

        return $this;
    }

    public function getStoredFilename(): string
    {
        return $this->storedFilename;
    }

    public function setStoredFilename(string $storedFilename): static
    {
        $this->storedFilename = $storedFilename;

        return $this;
    }

    public function isCompressed(): bool
    {
        return $this->isCompressed;
    }

    public function setIsCompressed(bool $isCompressed): static
    {
        $this->isCompressed = $isCompressed;

        return $this;
    }

    public function isHasThumbnail(): bool
    {
        return $this->hasThumbnail;
    }

    public function setHasThumbnail(bool $hasThumbnail): static
    {
        $this->hasThumbnail = $hasThumbnail;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUploadedFile(): ?UploadedFile
    {
        return $this->uploadedFile;
    }

    public function setUploadedFile(?UploadedFile $uploadedFile): static
    {
        $this->uploadedFile = $uploadedFile;

        return $this;
    }

    /** Retourne l'ID sous forme de chaîne pour le champ ImageField d'EasyAdmin */
    public function getThumbnailPath(): ?string
    {
        return null === $this->id ? null : (string) $this->id;
    }

    /** Taille originale lisible par l'humain */
    public function getFormattedSize(): string
    {
        $bytes = $this->size;
        if ($bytes < 1024) {
            return $bytes . ' o';
        }
        if ($bytes < 1_048_576) {
            return round($bytes / 1024, 1) . ' Ko';
        }

        return round($bytes / 1_048_576, 1) . ' Mo';
    }

    /** Taux de compression */
    public function getCompressionRatio(): string
    {
        if (0 === $this->size) {
            return '—';
        }
        $ratio = round((1 - $this->storedSize / $this->size) * 100);

        return $ratio > 0 ? "-{$ratio}%" : '—';
    }

    public function isVisibleOnWebsite(): ?bool
    {
        return $this->isVisibleOnWebsite;
    }

    public function setIsVisibleOnWebsite(bool $isVisibleOnWebsite): static
    {
        $this->isVisibleOnWebsite = $isVisibleOnWebsite;

        return $this;
    }
}
