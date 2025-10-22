<?php
/**
 * Gestionnaire de contrôleurs (Controller Handler)
 * 
 * Cette classe gère le routing et l'exécution des contrôleurs.
 * Elle analyse l'URL (paramètres GET controller et action) et appelle la méthode appropriée.
 * 
 * Fonctionnement :
 * 1. Récupère controller et action depuis l'URL (ex: ?controller=user&action=login)
 * 2. Construit le nom de la classe (ex: "userController")
 * 3. Charge le fichier du contrôleur
 * 4. Instancie la classe
 * 5. Exécute la méthode (action) demandée
 * 
 * @author SAE CyberCigales G5
 * @version 1.0
 */

final class controllerHandler
{
    /**
     * @var array Tableau contenant controller et action
     */
    private array $url;
    
    /**
     * @var array Paramètres additionnels du contrôleur
     */
    private array $params = [];

    /**
     * Constructeur - initialise le contrôleur et l'action
     * 
     * @param string|null $S_controller Nom du contrôleur (ex: "user")
     * @param string|null $S_action Nom de l'action (ex: "login")
     */
    public function __construct(?string $S_controller, ?string $S_action)
    {
        $this->url['controller'] = $this->controllerName($S_controller);
        $this->url['action'] = $this->actionName($S_action);
    }

    /**
     * Formate le nom du contrôleur
     * 
     * Ajoute le suffixe "Controller" et sécurise la chaîne (protection XSS)
     * Ex: "user" devient "userController"
     * 
     * @param string|null $controller Nom du contrôleur
     * @return string Nom du contrôleur formaté et sécurisé
     */
    private function controllerName(?string $controller): string
    {
        // Ajout du suffixe "Controller"
        $controller = ($controller) . 'Controller';

        // Protection XSS
        return htmlspecialchars($controller, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Formate le nom de l'action
     * 
     * Sécurise la chaîne et définit une action par défaut si vide.
     * 
     * @param string|null $action Nom de l'action
     * @return string Nom de l'action formaté et sécurisé
     */
    private function actionName(?string $action): string
    {
        // Action par défaut : "login"
        if (empty($action)) {
            return 'login';
        }

        // Protection XSS
        return htmlspecialchars($action, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Retourne les informations d'URL
     * 
     * @return array Tableau contenant controller et action
     */
    public function getUrl(): array
    {
        return $this->url;
    }

    /**
     * Exécute le contrôleur et l'action demandés
     * 
     * Cette méthode charge le fichier du contrôleur, instancie la classe
     * et exécute la méthode demandée. Elle gère aussi les erreurs.
     * 
     * @return void
     * @throws RuntimeException Si le contrôleur ou l'action n'existent pas
     */
    public function execute(): void
    {
        $controller = $this->url['controller'];
        $action = $this->url['action'];

        $controllerFile = Constant::indexDir() . '/Modules/controller/' . $controller . '.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        }

        if (!class_exists($controller)) {
            throw new RuntimeException("'$controller' est introuvable.");
        }

        $controllerInstance = new $controller();

        if (!method_exists($controllerInstance, $action)) {
            throw new RuntimeException("L'action '$action' est introuvable dans le contrôleur '$controller'.");
        }

        try {
            call_user_func_array([$controllerInstance, $action], []);
        } catch (Exception $e) {
            throw new RuntimeException("Erreur lors de l'exécution de l'action '$action' : " . $e->getMessage());
        }

        if (method_exists($controllerInstance, 'getParams')) {
            $this->params = $controllerInstance->getParams();
        }
    }

    /**
     * Retourne les paramètres du contrôleur
     * 
     * @return array Paramètres retournés par le contrôleur
     */
    public function getParams(): array
    {
        return $this->params;
    }
}