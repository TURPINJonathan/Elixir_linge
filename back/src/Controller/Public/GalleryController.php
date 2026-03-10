<?php

namespace App\Controller\Public;

use App\Entity\MediaFile;
use App\Service\FileStorageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/** API publique : galerie des médias visibles sur le site. */
#[Route('/api/public')]
class GalleryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FileStorageService $storage,
    ) {}

    /** Liste des médias visibles pour la galerie (id, alt, originalName, createdAt, hasThumbnail, mimeType). */
    #[Route('/gallery', name: 'public_gallery_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $repo = $this->em->getRepository(MediaFile::class);
        $medias = $repo->findVisibleOnWebsiteOrderByCreatedAt();

        $items = array_map(static function (MediaFile $m): array {
            return [
                'id' => $m->getId(),
                'alt' => $m->getAlt(),
                'originalName' => $m->getOriginalName(),
                'createdAt' => $m->getCreatedAt()->format(\DateTimeInterface::ATOM),
                'hasThumbnail' => $m->isHasThumbnail(),
                'mimeType' => $m->getMimeType(),
            ];
        }, $medias);

        return new JsonResponse(['items' => $items]);
    }

    /** Image pleine résolution d’un média visible (accès public, pas de vignette pour garder la qualité). */
    #[Route('/media/{id}/image', name: 'public_gallery_image', methods: ['GET'])]
    public function image(int $id): Response
    {
        $media = $this->em->getRepository(MediaFile::class)->find($id);
        if (!$media || !$media->isVisibleOnWebsite()) {
            throw $this->createNotFoundException('Média introuvable ou non visible.');
        }

        $content = $this->storage->getContent($media);
        $mime = $media->getMimeType();
        if (!str_starts_with($mime, 'image/')) {
            $mime = 'image/jpeg';
        }

        return new Response($content, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
