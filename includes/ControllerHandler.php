<?php

/**
 * ControllerHandler
 * - Résout le nom du contrôleur et de l'action à partir de la query.
 * - Sécurise et normalise les identifiants.
 */

declare(strict_types=1);

namespace SAE_CyberCigales_G5\includes;

use RuntimeException;
use SAE_CyberCigales_G5\Modules\controller\ButterflyWayController;

final class ControllerHandler
{
    /**
     * @var array Tableau contenant controller et action
     */
    private array $url;

    /** Paramètres collectés depuis le contrôleur exécuté. */
    private array $params = [];

    private static function log(string $message, string $type): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type);
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
        self::log("Résolution route -> controller={$this->url['controller']},
        action={$this->url['action']}", 'file');
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

        // On filtre les caractères autorisés pour éviter l'injection / traversal.
        if (!preg_match('/^[A-Za-z0-9_]+$/', $controller)) {
            $controller = 'Redirection';
        }

        // Suffixe standard.
        $controller .= 'Controller';

        // On retourne une valeur brute.
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
     * Exécute l'action du contrôleur.
     * - Charge le fichier si nécessaire (l'autoloader devrait déjà le faire).
     * - Instancie la classe contrôleur et appelle la méthode d'action.
     */
    public function execute(): void
    {
        $controller = $this->url['controller'];
        $action     = $this->url['action'];

        // Ceci est le namespace où sont rangés tous vos contrôleurs
        $controllerNamespace = 'SAE_CyberCigales_G5\\Modules\\controller\\';
        $FQCN = $controllerNamespace . $controller;

        // Vérifie l'existence de la classe contrôleur.(au cas où l'autoloader ne l'aurait pas déjà chargé).
        if (!class_exists($FQCN)) {
            self::log("Contrôleur introuvable: {$controller}", 'error');
            throw new RuntimeException("'{$controller}' est introuvable.");
        } else {
            log_console("Fichier contrôleur non trouvé
              (autoloader prendra le relais) : {$FQCN}", 'info');
        }

        $controllerInstance = new $FQCN();

        // Vérifie l'existence de l'action.
        if (!method_exists($controllerInstance, $action)) {
            self::log("Action introuvable: {$controller}::{$action}", 'error');
            throw new RuntimeException("L'action '{$action}' est introuvable dans le contrôleur '{$controller}'.");
        }

        // Exécute l'action.
        try {
            self::log("Exécution: {$controller}::{$action}()", 'file');
            call_user_func_array([$controllerInstance, $action], []);
        } catch (\Throwable $e) {
            self::log("Exception pendant {$controller}::{$action}() - " . $e->getMessage(), 'error');
            throw new RuntimeException("Erreur lors de l'exécution de l'action '{$action}' : "
                . $e->getMessage(), (int)$e->getCode(), $e);
        }

        // Récupère d'éventuels paramètres exposés par le contrôleur.
        if (method_exists($controllerInstance, 'getParams')) {
            $this->params = $controllerInstance->getParams();
            self::log("Params récupérés depuis {$controller}::getParams()", 'file');
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
