<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class IcdApiService
{
    /**
     * Return true when ICD API integration is enabled and base_url configured.
     */
    public function isEnabled(): bool
    {
        return (bool) config('bhcis.icd_api.enabled') && ! empty(config('bhcis.icd_api.base_url'));
    }

    /**
     * Search diagnoses using the configured ICD API. Returns array suitable for
     * the frontend autocomplete: [ {id, text}, ... ]
     */
    public function search(string $query, int $limit = 15): array
    {
        if (! $this->isEnabled() || trim($query) === '') {
            return [];
        }

        $token = $this->getAccessToken();
        if (! $token) {
            return [];
        }

        $base = rtrim(config('bhcis.icd_api.base_url'), '/');
        $path = config('bhcis.icd_api.search_path') ?: '/search';
        $url = $base . $path;

        try {
            $resp = Http::withToken($token)->get($url, [
                'q' => $query,
                'limit' => $limit,
            ]);

            if (! $resp->successful()) {
                return [];
            }

            $data = $resp->json();
            if (! is_array($data)) {
                return [];
            }

            $items = [];
            foreach ($data as $item) {
                if (is_string($item)) {
                    $items[] = ['id' => $item, 'text' => $item];
                    continue;
                }
                if (! is_array($item)) {
                    continue;
                }

                $code = $item['code'] ?? $item['diagnosis_code'] ?? ($item['id'] ?? null);
                $name = $item['name'] ?? $item['diagnosis_name'] ?? ($item['label'] ?? ($item['title'] ?? null));

                if (! $code && ! $name) {
                    continue;
                }

                $text = trim(($code ? $code.' - ' : '').($name ?? ''));
                $items[] = [
                    'id' => $code ?? $name,
                    'text' => $text,
                ];
            }

            return array_slice($items, 0, $limit);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Obtain an OAuth2 client_credentials access token and cache it.
     */
    private function getAccessToken(): ?string
    {
        $cacheKey = 'bhcis_icd_api_token';
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && ! empty($cached['access_token'])) {
            return $cached['access_token'];
        }

        $tokenUrl = config('bhcis.icd_api.token_url');
        if (empty($tokenUrl)) {
            return null;
        }

        try {
            $resp = Http::asForm()->post($tokenUrl, [
                'grant_type' => 'client_credentials',
                'client_id' => config('bhcis.icd_api.client_id'),
                'client_secret' => config('bhcis.icd_api.client_secret'),
            ]);

            if (! $resp->successful()) {
                return null;
            }

            $json = $resp->json();
            if (empty($json['access_token'])) {
                return null;
            }

            $expires = isset($json['expires_in']) ? intval($json['expires_in']) : 3600;
            Cache::put($cacheKey, $json, now()->addSeconds(max(60, $expires - 60)));

            return $json['access_token'];
        } catch (\Throwable $e) {
            return null;
        }
    }
}
