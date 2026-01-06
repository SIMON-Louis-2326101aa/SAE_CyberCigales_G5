<?php
namespace SAE_CyberCigales_G5\Modules\model;

use SAE_CyberCigales_G5\includes\ConnectionDB;

class EmailVerificationModel extends Database
{
    private ConnectionDB $db;

    public function __construct()
    {
        $this->db = ConnectionDB::getInstance();
    }

    public function generateAndStoreCode(string $email, int $ttlMinutes = 10): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $ttl = max(1, min(60, $ttlMinutes));

        $this->db->insert('email_verification_codes', [
            'email' => $email,
            'code' => $code,
            'expires_at' => date('Y-m-d H:i:s', time() + $ttl * 60),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $code;
    }

    public function checkCodeStatus(string $email, string $code): array
    {
        $rows = $this->db->getAll(
            'email_verification_codes',
            ['email' => $email, 'code' => $code],
            1
        );

        if (!$rows) {
            return [
                'valid' => false,
                'reason' => 'incorrect'
            ];
        }

        $row = $rows[0];

        if (strtotime($row['expires_at']) < time()) {
            return [
                'valid' => false,
                'reason' => 'expired'
            ];
        }

        return [
            'valid' => true,
            'reason' => 'valid'
        ];
    }
}
