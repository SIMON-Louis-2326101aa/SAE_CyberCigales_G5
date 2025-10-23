<?php
declare(strict_types=1);

/**
 * ControllerHandler
 * - Résout le nom du contrôleur et de l'action à partir de la query.
 * - Sécurise et normalise les identifiants.
 * - Instancie le contrôleur et exécute l'action demandée.
 * - Expose d'éventuels paramètres retournés par le contrôleur via getParams().
 */
final class controllerHandler
{
    /** Noms normalisés du contrôleur et de l'action. */
    private array $url;

    /** Paramètres collectés depuis le contrôleur exécuté. */
    private array $params = [];

    /**
     * @param ?string $S_controller Identifiant de contrôleur (ex: "user")
     * @param ?string $S_action     Identifiant d'action (ex: "login")
     */
    public function __construct(?string $S_controller, ?string $S_action)
    {
        $this->url['controller'] = $this->controllerName($S_controller);
        $this->url['action']     = $this->actionName($S_action);

        if (function_exists('log_console')) {
            log_console("Résolution route -> controller={$this->url['controller']}, action={$this->url['action']}", 'file'); // 📄
        }
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
            $controller = 'redirection';
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

        // Emplacement attendu du fichier contrôleur (au cas où l'autoloader ne l'aurait pas déjà chargé).
        $controllerFile = Constant::indexDir() . '/Modules/controller/' . $controller . '.php';

        if (is_file($controllerFile)) {
            require_once $controllerFile;
            if (function_exists('log_console')) {
                log_console("Chargement contrôleur (require): {$controllerFile}", 'file'); // 📄
            }
        } else {
            if (function_exists('log_console')) {
                log_console("Fichier contrôleur non trouvé (autoloader prendra le relais) : {$controllerFile}", 'info'); // ℹ️
            }
        }

        // Vérifie l'existence de la classe contrôleur.
        if (!class_exists($controller)) {
            if (function_exists('log_console')) {
                log_console("Contrôleur introuvable: {$controller}", 'error'); // ❌
            }
            throw new RuntimeException("'{$controller}' est introuvable.");
        }

        $controllerInstance = new $controller();

        // Vérifie l'existence de l'action.
        if (!method_exists($controllerInstance, $action)) {
            if (function_exists('log_console')) {
                log_console("Action introuvable: {$controller}::{$action}", 'error'); // ❌
            }
            throw new RuntimeException("L'action '{$action}' est introuvable dans le contrôleur '{$controller}'.");
        }

        // Exécute l'action.
        try {
            if (function_exists('log_console')) {
                log_console("Exécution: {$controller}::{$action}()", 'file'); // 📄
            }
            call_user_func_array([$controllerInstance, $action], []);
        } catch (\Throwable $e) {
            if (function_exists('log_console')) {
                log_console("Exception pendant {$controller}::{$action}() - " . $e->getMessage(), 'error'); // ❌
            }
            throw new RuntimeException("Erreur lors de l'exécution de l'action '{$action}' : " . $e->getMessage(), (int)$e->getCode(), $e);
        }

        // Récupère d'éventuels paramètres exposés par le contrôleur.
        if (method_exists($controllerInstance, 'getParams')) {
            $this->params = $controllerInstance->getParams();
            if (function_exists('log_console')) {
                log_console("Params récupérés depuis {$controller}::getParams()", 'file'); // 📄
            }
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
