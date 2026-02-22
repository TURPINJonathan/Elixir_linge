<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {}

    /**
     * Géocode une adresse française via l'API IGN (data.geopf.fr).
     * Retourne [latitude, longitude] ou null si non trouvé.
     */
    public function geocode(string $fullAddress): ?array
    {
        $address = trim($fullAddress);

        if (empty($address)) {
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', 'https://data.geopf.fr/geocodage/search', [
                'query' => [
                    'q'     => $address,
                    'limit' => 1,
                ],
            ]);

            $data = $response->toArray();

            if (isset($data['features'][0]['geometry']['coordinates'])) {
                // Format GeoJSON : [longitude, latitude]
                $coords = $data['features'][0]['geometry']['coordinates'];

                return [
                    'latitude'  => (string) $coords[1],
                    'longitude' => (string) $coords[0],
                ];
            }
        } catch (\Exception $e) {
            // Log silencieusement l'erreur (optionnel)
            error_log('Geocoding error: ' . $e->getMessage());
        }

        return null;
    }
}
