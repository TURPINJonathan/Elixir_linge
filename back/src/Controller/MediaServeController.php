<?php

namespace App\Controller;

use App\Entity\MediaFile;
use App\Service\FileStorageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Endpoints de téléchargement et de vignette des médias.
 * Protégés par le firewall "main" (ROLE_ADMIN) via access_control.
 */
#[Route('/backoffice/media')]
class MediaServeController extends AbstractController
{
    public function __construct(
        private readonly FileStorageService $storage,
        private readonly EntityManagerInterface $em,
    ) {}

    /** Téléchargement du fichier original (décompressé). */
    #[Route('/{id}/download', name: 'media_download', methods: ['GET'])]
    public function download(int $id): Response
    {
        $media = $this->em->getRepository(MediaFile::class)->find($id);
        if (!$media) {
            throw $this->createNotFoundException('Média introuvable.');
        }

        $content = $this->storage->getContent($media);

        return new Response($content, 200, [
            'Content-Type'        => $media->getMimeType(),
            'Content-Disposition' => \sprintf(
                'attachment; filename="%s"',
                addslashes($media->getOriginalName()),
            ),
            'Content-Length'      => \strlen($content),
        ]);
    }

    /** Vignette inline (WebP pour les images, SVG placeholder pour les autres). */
    #[Route('/thumbnail/{id}', name: 'media_thumbnail', methods: ['GET'])]
    public function thumbnail(int $id): Response
    {
        $media = $this->em->getRepository(MediaFile::class)->find($id);
        if (!$media) {
            throw $this->createNotFoundException('Média introuvable.');
        }

        if ($media->isHasThumbnail()) {
            $content = $this->storage->getThumbnailContent($media);
            if (null !== $content) {
                return new Response($content, 200, [
                    'Content-Type'  => 'image/webp',
                    'Cache-Control' => 'private, max-age=86400',
                ]);
            }
        }

        // Placeholder SVG selon le type MIME
        $svg = $this->buildPlaceholderSvg($media->getMimeType(), $media->getOriginalName());

        return new Response($svg, 200, [
            'Content-Type'  => 'image/svg+xml',
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    private function buildPlaceholderSvg(string $mimeType, string $filename): string
    {
        [$color, $icon, $label] = match (true) {
            str_starts_with($mimeType, 'video/')       => ['#3b82f6', '▶', 'VIDEO'],
            str_starts_with($mimeType, 'audio/')       => ['#10b981', '♪', 'AUDIO'],
            'application/pdf' === $mimeType            => ['#ef4444', 'PDF', 'PDF'],
            str_starts_with($mimeType, 'application/') => ['#8b5cf6', '⬜', 'FICHIER'],
            str_starts_with($mimeType, 'text/')        => ['#f59e0b', '≡', 'TEXTE'],
            default                                    => ['#6b7280', '⬜', 'FICHIER'],
        };

        $ext = strtoupper(pathinfo($filename, \PATHINFO_EXTENSION));
        if ('' !== $ext) {
            $label = $ext;
        }

        $escapedLabel = htmlspecialchars($label, \ENT_XML1);

        return <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="200" height="200">
                <rect width="200" height="200" rx="12" fill="{$color}" opacity="0.15"/>
                <rect width="200" height="200" rx="12" fill="none" stroke="{$color}" stroke-width="2"/>
                <text x="100" y="90" font-family="system-ui,sans-serif" font-size="48"
                      text-anchor="middle" dominant-baseline="middle" fill="{$color}">{$icon}</text>
                <text x="100" y="148" font-family="system-ui,sans-serif" font-size="22" font-weight="600"
                      text-anchor="middle" dominant-baseline="middle" fill="{$color}">{$escapedLabel}</text>
            </svg>
            SVG;
    }
}
