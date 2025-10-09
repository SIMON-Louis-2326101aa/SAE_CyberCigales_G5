<?php
require_once __DIR__ . '/database.php';

require_once __DIR__ . '/../../includes/connexionDB.php';
class accountModel extends database
{
    private connexionDB $db;
    public function __construct() {
        $this->db = connexionDB::getInstance();
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
