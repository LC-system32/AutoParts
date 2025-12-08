<?php

declare(strict_types=1);

namespace App\Core;

final class ApiClient
{

    private const BASE_URL = 'http://host.docker.internal:3000';
    /**
     * @return string[]
     */
    private static function buildHeaders(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
        ];


        $token = $_SESSION['api_token'] ?? null;

        if (!empty($token)) {


            $headers[] = 'Authorization: Bearer ' . $token;
            $headers[] = 'X-AUTH: ' . $token;
        }

        return $headers;
    }
    /**
     * @param string              $method   GET|POST|PUT|PATCH|DELETE
     * @param string              $endpoint напр. '/api/addresses'
     * @param array<string,mixed> $data
     */
    public static function request(string $method, string $endpoint, array $data = []): array
    {
        $url = rtrim(self::BASE_URL, '/') . '/' . ltrim($endpoint, '/');

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_HTTPHEADER     => self::buildHeaders(),
            CURLOPT_TIMEOUT        => 10,
        ]);

        if (!empty($data) && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'], true)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        $raw  = curl_exec($ch);
        $info = curl_getinfo($ch);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new \RuntimeException('API error: ' . $err);
        }

        $status  = (int)($info['http_code'] ?? 0);
        $decoded = json_decode($raw, true);


        if ($status < 200 || $status >= 300) {
            $msg = 'HTTP ' . $status;

            if (is_array($decoded)) {
                if (!empty($decoded['error'])) {
                    $msg = $decoded['error'];
                } elseif (!empty($decoded['message'])) {
                    $msg = $decoded['message'];
                }
            }

            throw new \RuntimeException('API error: ' . $msg);
        }

        if (!is_array($decoded)) {
            return [];
        }

        if (array_key_exists('data', $decoded)) {
            return $decoded['data'];
        }

        return $decoded;
    }

    public static function get(string $endpoint, array $params = []): array
    {
        if (!empty($params)) {
            $endpoint .= (str_contains($endpoint, '?') ? '&' : '?') . http_build_query($params);
        }

        return self::request('GET', $endpoint);
    }

    public static function post(string $endpoint, array $data = []): array
    {
        return self::request('POST', $endpoint, $data);
    }

    public static function put(string $endpoint, array $data = []): array
    {
        return self::request('PUT', $endpoint, $data);
    }

    public static function delete(string $endpoint, array $data = []): array
    {
        return self::request('DELETE', $endpoint, $data);
    }

    public static function patch(string $endpoint, array $data = []): array
    {
        return self::request('PATCH', $endpoint, $data);
    }
}
