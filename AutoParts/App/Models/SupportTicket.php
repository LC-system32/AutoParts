<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use RuntimeException;

/**
 * SupportTicket Model
 */
class SupportTicket extends Model
{
    public static function createTicket(array $data): array
    {
        $resp = self::create('/api/support', [
            'subject' => (string)($data['subject'] ?? ''),
            'message' => (string)($data['message'] ?? ''),
        ]);
        if (!is_array($resp)) {
            throw new RuntimeException('API error: failed to create ticket');
        }
        return $resp;
    }

    public static function listForCurrentUser(): array
    {
        $data = self::getList('/api/support');
        return is_array($data) ? $data : [];
    }

    public static function findForCurrentUser(int $id): array
    {
        $data = self::get('/api/support/' . $id);
        if (!is_array($data)) {
            throw new RuntimeException('API error: ticket not found');
        }

        if (isset($data['data']) && is_array($data['data'])) {
            $data = $data['data'];
        }
        return $data;
    }

    public static function addMessage(int $ticketId, string $message): array
    {
        $resp = self::create('/api/support/' . $ticketId . '/messages', [
            'message' => $message,
        ]);
        if (!is_array($resp)) {
            throw new RuntimeException('API error: failed to add message');
        }
        return $resp;
    }
}
