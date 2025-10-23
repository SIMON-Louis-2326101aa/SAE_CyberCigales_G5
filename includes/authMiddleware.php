<?php
/**
 * Middleware d'authentification
 * Protège les pages qui nécessitent une connexion utilisateur
 */

require_once __DIR__ . '/securityHelper.php';

class AuthMiddleware
{
    /**
     * Pages publiques qui ne nécessitent pas d'authentification
     */
    private static array $publicPages = [
        'openHomepage',
        'openFormConnection',
        'openFormRegister',
        'openForgotPwd',
        'openChangePwd',
        'openLegalMention',
        'openAbout'
    ];

    /**
     * Vérifie si l'utilisateur a accès à la page demandée
     * @param string $action L'action demandée
     * @return bool True si l'accès est autorisé
     */
    public static function checkAccess(string $action): bool
    {
        // Si la page est publique, autoriser l'accès
        if (in_array($action, self::$publicPages)) {
            return true;
        }

        // Sinon, vérifier si l'utilisateur est connecté
        if (!SecurityHelper::isLoggedIn()) {
            // Rediriger vers la page de connexion
            header("Location: index.php?controller=redirection&action=openFormConnection");
            exit();
        }

        return true;
    }

    /**
     * Vérifie si l'utilisateur est déjà connecté
     * Utile pour rediriger depuis les pages de connexion/inscription
     */
    public static function isAlreadyLoggedIn(): bool
    {
        return SecurityHelper::isLoggedIn();
    }

    /**
     * Redirige vers la homepage si l'utilisateur est déjà connecté
     */
    public static function redirectIfLoggedIn(): void
    {
        if (self::isAlreadyLoggedIn()) {
            header("Location: index.php?controller=redirection&action=openHomepage");
            exit();
        }
    }
}
