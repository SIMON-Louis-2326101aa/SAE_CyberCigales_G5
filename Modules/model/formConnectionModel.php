<?php

class formConnectionModel extends database
{
    public function authenticate($email, $password) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :mail";
        $stmt = $this->getBdd()->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }
}