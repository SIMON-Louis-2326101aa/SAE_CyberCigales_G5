<?php

/**
 * ControllerHandler
 * - Résout le nom du contrôleur et de l'action à partir de la query.
 * - Sécurise et normalise les identifiants.
 */

declare(strict_types=1);

namespace SAE_CyberCigales_G5\includes;

use RuntimeException;

final class ControllerHandler
{
    /**
     * @var array Tableau contenant controller et action
     */
    private array $url;

    /** Paramètres collectés depuis le contrôleur exécuté. */
    private array $params = [];

    private static function log(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
        }
    }

    /**
     * @param ?string $S_controller Identifiant de contrôleur (ex: "user")
     * @param ?string $S_action     Identifiant d'action (ex: "login")
     */
    public function __construct(?string $S_controller, ?string $S_action)
    {
        $this->url['controller'] = $this->controllerName($S_controller);
        $this->url['action']     = $this->actionName($S_action);

        self::log(
            "Résolution route",
            'file',
            [
                'controller' => $this->url['controller'],
                'action' => $this->url['action'],
            ]
        );
    }

    /**
     * Normalise et sécurise le nom de contrôleur.
     * - Garde uniquement lettres, chiffres et underscore.
     * - Ajoute le suffixe "Controller".
     * - Applique un fallback si vide ou invalide.
     */
    private function controllerName(?string $controller): string
    {
        $controller = (string)($controller ?? '');

        if (!preg_match('/^[A-Za-z0-9_]+$/', $controller)) {
            self::log('Nom de contrôleur invalide détecté, fallback appliqué', 'warn', [
                'raw_controller' => $controller,
            ]);
            $controller = 'Redirection';
        }

        $controller .= 'Controller';

        return $controller;
    }

    /**
     * Normalise et sécurise le nom d'action.
     * - Garde uniquement lettres, chiffres et underscore.
     * - Fallback vers "login" si vide/invalide.
     */
    private function actionName(?string $action): string
    {
        $action = (string)($action ?? '');

        if ($action === '' || !preg_match('/^[A-Za-z0-9_]+$/', $action)) {
            self::log('Nom d’action invalide détecté, fallback appliqué', 'warn', [
                'raw_action' => $action,
            ]);
            return 'login';
        }

        return $action;
    }

    /**
     * Retourne le couple controller/action résolu.
     */
    public function getUrl(): array
    {
        return $this->url;
    }

    /**
     * Affiche la page 404 sans passer par le catch global.
     */
    private function render404(string $reason, array $context = []): void
    {
        http_response_code(404);

        self::log('404 déclenchée', 'warn', array_merge([
            'reason' => $reason,
            'controller' => $this->url['controller'] ?? null,
            'action' => $this->url['action'] ?? null,
        ], $context));

        $notFoundFile = dirname(__DIR__) . '/404.php';

        if (is_file($notFoundFile)) {
            require $notFoundFile;
            return;
        }

        // Fallback si le fichier 404.php est introuvable
        echo "<main>";
        echo "<h1>Erreur 404 - Page non trouvée</h1>";
        echo "<p>Désolé, la page que vous cherchez n'existe pas ou a été déplacée.</p>";
        echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
        echo "</main>";
    }

    /**
     * Exécute l'action du contrôleur.
     * - Charge le fichier si nécessaire (l'autoloader devrait déjà le faire).
     * - Instancie la classe contrôleur et appelle la méthode d'action.
     */
    public function execute(): void
    {
        $controller = $this->url['controller'];
        $action     = $this->url['action'];

        $controllerNamespace = 'SAE_CyberCigales_G5\\Modules\\controller\\';
        $FQCN = $controllerNamespace . $controller;

        // Contrôleur introuvable => 404
        if (!class_exists($FQCN)) {
            self::log("Contrôleur introuvable", 'warn', [
                'controller' => $controller,
                'fqcn' => $FQCN,
            ]);
            $this->render404('controller_not_found', [
                'fqcn' => $FQCN,
            ]);
            return;
        } else {
            self::log('Contrôleur trouvé', 'info', [
                'controller' => $controller,
                'fqcn' => $FQCN,
            ]);
        }

        $controllerInstance = new $FQCN();

        // Action introuvable => 404
        if (!method_exists($controllerInstance, $action)) {
            self::log("Action introuvable", 'warn', [
                'controller' => $controller,
                'action' => $action,
            ]);
            $this->render404('action_not_found');
            return;
        }

        // Exécute l'action
        try {
            self::log("Exécution contrôleur/action", 'file', [
                'controller' => $controller,
                'action' => $action,
            ]);

            call_user_func_array([$controllerInstance, $action], []);
        } catch (\Throwable $e) {
            self::log("Exception pendant exécution contrôleur/action", 'error', [
                'controller' => $controller,
                'action' => $action,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw new RuntimeException(
                "Erreur lors de l'exécution de l'action '{$action}' : " . $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }

        // Récupère d'éventuels paramètres exposés par le contrôleur
        if (method_exists($controllerInstance, 'getParams')) {
            $this->params = $controllerInstance->getParams();

            self::log("Params récupérés depuis le contrôleur", 'file', [
                'controller' => $controller,
                'count' => count($this->params),
            ]);
        }
    }

    /**
     * Paramètres retournés par le contrôleur exécuté.
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
