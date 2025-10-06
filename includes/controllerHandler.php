<?php

final class controllerHandler
{
    private array $url;
    private array $params = [];

    public function __construct(?string $S_controller, ?string $S_action)
    {
        $this->url['controller'] = $this->controllerName($S_controller);
        $this->url['action'] = $this->actionName($S_action);
    }

    private function controllerName(?string $controller): string
    {

        $controller = ($controller) . 'Controller';

        return htmlspecialchars($controller, ENT_QUOTES, 'UTF-8');
    }

    private function actionName(?string $action): string
    {
        if (empty($action)) {
            return 'login';
        }

        $action = $action ;


        return htmlspecialchars($action, ENT_QUOTES, 'UTF-8');
    }

    public function getUrl(): array
    {
        return $this->url;
    }


    public function execute(): void
    {
        $controller = $this->url['controller'];
        $action = $this->url['action'];

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

    public function getParams(): array
    {
        return $this->params;
    }
}