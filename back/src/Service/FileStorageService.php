<?php

namespace App\Service;

use App\Entity\MediaFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileStorageService
{
    private string $mediaDir;
    private string $thumbnailDir;

    public function __construct(string $projectDir)
    {
        $this->mediaDir     = $projectDir . '/var/uploads/media/';
        $this->thumbnailDir = $projectDir . '/var/uploads/thumbnails/';

        foreach ([$this->mediaDir, $this->thumbnailDir] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * Traite et stocke le fichier uploadé dans $media.
     * Remplit toutes les propriétés de $media sauf id/createdAt/updatedAt/alt.
     */
    public function store(MediaFile $media, UploadedFile $file): void
    {
        $storedFilename = bin2hex(random_bytes(16));
        $mimeType       = $file->getMimeType() ?? 'application/octet-stream';
        $originalContent = file_get_contents($file->getPathname());
        $originalSize    = strlen($originalContent);

        $isCompressed = false;
        $hasThumbnail = false;

        if (str_starts_with($mimeType, 'image/')) {
            // Conversion WebP + redimensionnement max 2000px
            $storedContent = $this->convertToWebP($originalContent);
            if (false === $storedContent) {
                // Fallback : stocker tel quel si GD échoue
                $storedContent = $originalContent;
            }
            // Vignette 200×200
            $this->generateThumbnail($originalContent, $storedFilename);
            $hasThumbnail = true;
        } else {
            // gzcompress uniquement si ça réduit la taille
            $compressed = gzcompress($originalContent, 6);
            if (false !== $compressed && strlen($compressed) < $originalSize) {
                $storedContent = $compressed;
                $isCompressed  = true;
            } else {
                $storedContent = $originalContent;
            }
        }

        file_put_contents($this->mediaDir . $storedFilename, $storedContent);

        $media->setOriginalName($file->getClientOriginalName());
        $media->setMimeType($mimeType);
        $media->setSize($originalSize);
        $media->setStoredSize(strlen($storedContent));
        $media->setStoredFilename($storedFilename);
        $media->setIsCompressed($isCompressed);
        $media->setHasThumbnail($hasThumbnail);
    }

    /**
     * Retourne le contenu décompressé/original du fichier.
     */
    public function getContent(MediaFile $media): string
    {
        $path = $this->mediaDir . $media->getStoredFilename();
        if (!file_exists($path)) {
            throw new \RuntimeException("Fichier introuvable : {$media->getStoredFilename()}");
        }
        $content = file_get_contents($path);

        return $media->isCompressed() ? gzuncompress($content) : $content;
    }

    /**
     * Retourne le contenu de la vignette (WebP), ou null si elle n'existe pas.
     */
    public function getThumbnailContent(MediaFile $media): ?string
    {
        if (!$media->isHasThumbnail()) {
            return null;
        }
        $path = $this->thumbnailDir . $media->getStoredFilename() . '.webp';

        return file_exists($path) ? file_get_contents($path) : null;
    }

    /**
     * Supprime les fichiers physiques associés au média.
     */
    public function delete(MediaFile $media): void
    {
        $path = $this->mediaDir . $media->getStoredFilename();
        if (file_exists($path)) {
            unlink($path);
        }

        if ($media->isHasThumbnail()) {
            $thumbPath = $this->thumbnailDir . $media->getStoredFilename() . '.webp';
            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }
        }
    }

    // -------------------------------------------------------------------------
    // Méthodes privées
    // -------------------------------------------------------------------------

    private function convertToWebP(string $content): string|false
    {
        $im = @imagecreatefromstring($content);
        if (false === $im) {
            return false;
        }

        // Redimensionnement si > 2000px
        $origW = imagesx($im);
        $origH = imagesy($im);
        $maxSize = 2000;

        if ($origW > $maxSize || $origH > $maxSize) {
            $ratio = min($maxSize / $origW, $maxSize / $origH);
            $newW  = max(1, (int) ($origW * $ratio));
            $newH  = max(1, (int) ($origH * $ratio));

            $resized = imagecreatetruecolor($newW, $newH);
            // Préserver la transparence
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefill($resized, 0, 0, $transparent);

            imagecopyresampled($resized, $im, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($im);
            $im = $resized;
        }

        ob_start();
        imagewebp($im, null, 80);
        $webpContent = ob_get_clean();
        imagedestroy($im);

        return $webpContent ?: false;
    }

    private function generateThumbnail(string $content, string $storedFilename): void
    {
        $im = @imagecreatefromstring($content);
        if (false === $im) {
            return;
        }

        $origW    = imagesx($im);
        $origH    = imagesy($im);
        $thumbMax = 200;

        $ratio = min($thumbMax / $origW, $thumbMax / $origH);
        $newW  = max(1, (int) ($origW * $ratio));
        $newH  = max(1, (int) ($origH * $ratio));

        $thumb = imagecreatetruecolor($newW, $newH);
        // Préserver la transparence
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $transparent = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
        imagefill($thumb, 0, 0, $transparent);

        imagecopyresampled($thumb, $im, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagedestroy($im);

        imagewebp($thumb, $this->thumbnailDir . $storedFilename . '.webp', 75);
        imagedestroy($thumb);
    }
}
