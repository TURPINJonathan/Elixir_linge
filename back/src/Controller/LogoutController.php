<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LogoutController
{
    public function __construct(
        private readonly RefreshTokenManagerInterface $refreshTokenManager,
    ) {}

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        // 1) Récupérer le refresh token (priorité cookie, fallback JSON body)
        $token = $request->cookies->get('refresh_token');

        if (!$token) {
            $data = json_decode($request->getContent() ?: '', true);
            $token = is_array($data) ? ($data['refresh_token'] ?? null) : null;
        }

        // 2) Révocation DB (strict) si on a un token
        if (is_string($token) && $token !== '') {
            $rt = $this->refreshTokenManager->get($token);
            if ($rt) {
                $this->refreshTokenManager->delete($rt);
            }
        }

        // 3) Supprimer le cookie côté client
        // IMPORTANT: doit matcher exactement les attributs utilisés lors du set (path, domain, secure, samesite)
        $response = new JsonResponse(['ok' => true], Response::HTTP_OK);

        $response->headers->setCookie(
            Cookie::create('refresh_token')
                ->withValue('')
                ->withExpires(1)
                ->withPath('/')
                ->withHttpOnly(true)
                ->withSecure(false)
                ->withSameSite(Cookie::SAMESITE_LAX));

        $response->headers->setCookie(
            Cookie::create('refresh_token')
                ->withValue('')
                ->withExpires(1) // date passée
                ->withPath('/')  // adapte si tu as mis autre chose
                ->withHttpOnly(true)
                ->withSecure(false) // dev http -> false ; prod https -> true
                ->withSameSite(Cookie::SAMESITE_LAX)
        );

        return $response;
    }
}