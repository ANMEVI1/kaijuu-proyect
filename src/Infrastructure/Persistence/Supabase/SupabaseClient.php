<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Supabase;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Cliente base para comunicarse con la REST API de Supabase.
 */
class SupabaseClient
{
    private Client $http;
    private string $url;
    private string $anonKey;

    public function __construct()
    {
        $this->url     = rtrim($_ENV['SUPABASE_URL'], '/');
        // Use service key to bypass RLS in the backend, fallback to anon key
        $key = $_ENV['SUPABASE_SERVICE_KEY'] ?? $_ENV['SUPABASE_ANON_KEY'];

        $this->http = new Client([
            'base_uri' => $this->url . '/rest/v1/',
            'headers'  => [
                'apikey'        => $key,
                'Authorization' => 'Bearer ' . $key,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Prefer'        => 'return=representation',
            ],
            'curl' => [
                \CURLOPT_IPRESOLVE => \CURL_IPRESOLVE_V4,
            ],
            'verify' => false, // Speed up local dev by bypassing SSL checks if unnecessary
        ]);
    }

    /** GET /rest/v1/{table}?select=...&{filters} */
    public function get(string $table, array $params = []): array
    {
        try {
            $res = $this->http->get($table, ['query' => $params]);
            return json_decode((string)$res->getBody(), true) ?? [];
        } catch (GuzzleException $e) {
            return [];
        }
    }

    /** POST /rest/v1/{table} */
    public function post(string $table, array $body): array
    {
        try {
            $res = $this->http->post($table, ['json' => $body]);
            $data = json_decode((string)$res->getBody(), true) ?? [];
            return $data[0] ?? $data;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorBody = (string) $e->getResponse()->getBody();
            throw new \RuntimeException("Supabase POST error: " . $errorBody);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Connection error: " . $e->getMessage());
        }
    }

    /** PATCH /rest/v1/{table}?id=eq.{id} */
    public function patch(string $table, string $id, array $body): array
    {
        try {
            $res = $this->http->patch($table, [
                'query' => ['id' => "eq.$id"],
                'json'  => $body,
            ]);
            $data = json_decode((string)$res->getBody(), true) ?? [];
            return $data[0] ?? $data;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorBody = (string)$e->getResponse()->getBody();
            error_log("Supabase PATCH error on table $table ($id): " . $errorBody);
            throw new \RuntimeException("Supabase PATCH error: " . $errorBody);
        } catch (GuzzleException $e) {
            error_log("Supabase PATCH connection error on $table: " . $e->getMessage());
            throw new \RuntimeException("Connection error: " . $e->getMessage());
        }
    }

    /** DELETE /rest/v1/{table}?id=eq.{id} */
    public function delete(string $table, string $id): void
    {
        try {
            $this->http->delete($table, [
                'query' => ['id' => "eq.$id"],
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorBody = (string)$e->getResponse()->getBody();
            error_log("Supabase DELETE error on table $table ($id): " . $errorBody);
            throw new \RuntimeException("Supabase DELETE error: " . $errorBody);
        } catch (GuzzleException $e) {
            error_log("Supabase DELETE connection error on $table: " . $e->getMessage());
            throw new \RuntimeException("Connection error: " . $e->getMessage());
        }
    }
}
