<?php
require_once __DIR__ . '/database.php';

require_once __DIR__ . '/../../includes/connectionDB.php';
class accountModel extends database
{
    private connectionDB $db;
    public function __construct() {
        $this->db = connectionDB::getInstance();
    }
    public function delete(string $email): bool
    {
        $sql = "DELETE FROM users WHERE email = :email";
        $stmt = $this->getBdd()->prepare($sql);
        return $stmt->execute([
            'email' => $email,
        ]);
    }
}
