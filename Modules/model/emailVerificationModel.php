<?php

require_once __DIR__ . '/database.php';

class emailVerificationModel extends database
{
    public function __construct()
    {
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS email_verification_codes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            code VARCHAR(10) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->getBdd()->exec($sql);
    }

    public function generateAndStoreCode(string $email, int $ttlMinutes = 10): string
    {
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = (new DateTimeImmutable("+{$ttlMinutes} minutes"))->format('Y-m-d H:i:s');

        $stmt = $this->getBdd()->prepare(
            'INSERT INTO email_verification_codes (email, code, expires_at, created_at) VALUES (:email, :code, :expires_at, NOW())'
        );
        $stmt->execute([
            'email' => $email,
            'code' => $code,
            'expires_at' => $expiresAt,
        ]);

        return $code;
    }

    public function validateCode(string $email, string $code): bool
    {
        $stmt = $this->getBdd()->prepare(
            'SELECT id FROM email_verification_codes WHERE email = :email AND code = :code AND expires_at >= NOW() ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute([
            'email' => $email,
            'code' => $code,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return !empty($row);
    }
}


