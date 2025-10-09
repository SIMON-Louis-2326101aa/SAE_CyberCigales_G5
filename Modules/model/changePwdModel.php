<?php
require_once __DIR__ . '/database.php';

require_once __DIR__ . '/../../includes/connexionDB.php';
class changePwdModel extends database
{
    private connexionDB $db;
    public function __construct() {
        $this->db = connexionDB::getInstance();
    }
    public function changePwd(string $newPassword, string $email): bool
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $bdd = $this->getBdd();
        $sql = "UPDATE users SET  password = :newPassword WHERE email = :email";
        $stmt = $bdd->prepare($sql);
        return $stmt->execute([
            'newPassword' => $hashedPassword,
            'email' => $email
        ]);

    }

}