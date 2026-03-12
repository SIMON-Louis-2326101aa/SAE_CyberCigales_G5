<?php

namespace SAE_CyberCigales_G5\Modules\controller;

use RuntimeException;
use SAE_CyberCigales_G5\includes\ConnectionDB;
use SAE_CyberCigales_G5\includes\ViewHandler;
use SAE_CyberCigales_G5\Modules\model\LoginAttemptModel;

class CrashTestController
{
    private function logCrash(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
        }
    }

    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $adminEmail = $_ENV['ADMIN_EMAIL'] ?? null;

        if (!isset($_SESSION['email']) || $_SESSION['email'] !== $adminEmail) {
            $this->logCrash('Accès crash test refusé', 'warn', [
                'email' => $_SESSION['email'] ?? null,
                'uri' => $_SERVER['REQUEST_URI'] ?? null,
            ]);

            $_SESSION['flash_error'] = "Accès réservé à l'administrateur.";
            header("Location: index.php?controller=Redirection&action=openHomepage");
            exit;
        }
    }

    public function index(): void
    {
        $this->requireAdmin();

        $this->logCrash('Page crash test affichée', 'info', [
            'email' => $_SESSION['email'] ?? null,
        ]);

        ViewHandler::show('admin/crashTestView', [
            'pageTitle' => 'Crash Test Admin'
        ]);
    }

    public function throwException(): void
    {
        $this->requireAdmin();

        $this->logCrash('Crash test: exception volontaire', 'warn');
        throw new RuntimeException('Crash test: exception volontaire.');
    }

    public function callUndefinedFunction(): void
    {
        $this->requireAdmin();

        $this->logCrash('Crash test: fonction inexistante', 'warn');
        fonctionQuiNExistePasCrashTest();
    }

    public function callMethodOnNull(): void
    {
        $this->requireAdmin();

        $this->logCrash('Crash test: appel méthode sur null', 'warn');
        $x = null;
        $x->boom();
    }

    public function invalidView(): void
    {
        $this->requireAdmin();

        $this->logCrash('Crash test: vue inexistante', 'warn');
        ViewHandler::show('admin/vueQuiNExistePasDuTout');
    }

    public function invalidControllerTarget(): void
    {
        $this->requireAdmin();

        $this->logCrash('Crash test: redirection vers contrôleur/action invalides', 'warn');
        header("Location: index.php?controller=ControleurImaginaire&action=actionFantome");
        exit;
    }

    public function dbFailure(): void
    {
        $this->requireAdmin();

        $this->logCrash('Crash test: requête SQL invalide', 'warn');

        $pdo = ConnectionDB::getInstance()->getPdo();
        $pdo->query('SELECT * FROM table_totalement_inexistante_crash_test');
    }

    public function userError(): void
    {
        $this->requireAdmin();

        $this->logCrash('Crash test: trigger_error fatal', 'warn');
        trigger_error('Crash test: E_USER_ERROR volontaire.', E_USER_ERROR);
    }

    public function typeError(): void
    {
        $this->requireAdmin();

        $this->logCrash('Crash test: TypeError volontaire', 'warn');
        $this->strictIntMethod("texte");
    }

    private function strictIntMethod(int $value): int
    {
        return $value * 2;
    }

    public function test404(): void
    {
        $this->requireAdmin();

        $this->logCrash('Crash test: 404 volontaire', 'warn', [
            'uri' => $_SERVER['REQUEST_URI'] ?? null,
        ]);

        http_response_code(404);
        echo "<main><h1>404 - Page introuvable (Crash Test)</h1>
        <p>Erreur volontaire déclenchée par l'administrateur.</p></main>";
        exit;
    }

    public function brokenRedirect(): void
    {
        $this->requireAdmin();

        $this->logCrash('Crash test: redirection cassée', 'warn');
        header("Location: /page-qui-nexiste-absolument-pas-123456");
        exit;
    }

    public function bruteForceTest(): void
    {
        $this->requireAdmin();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $email = $_GET['email'] ?? ($_SESSION['email'] ?? '');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $loginAttemptModel = new LoginAttemptModel();

        for ($i = 0; $i < 6; $i++) {
            $loginAttemptModel->recordFailedAttempt($email, $ip);
        }

        $accountState = $loginAttemptModel->isAccountBlocked($email);
        $ipState = $loginAttemptModel->isIPBlocked($ip);

        $this->logCrash('Crash test: brute force simulé', 'warn', [
            'email' => $email,
            'ip' => $ip,
            'attempts_added' => 6,
            'account_blocked' => $accountState['blocked'] ?? false,
            'account_attempts' => $accountState['attempts'] ?? 0,
            'ip_blocked' => $ipState['blocked'] ?? false,
            'ip_attempts' => $ipState['attempts'] ?? 0,
        ]);

        $_SESSION['flash_success'] = "Test brute force exécuté sur {$email}. Vérifie les logs et l'état de blocage.";
        header("Location: index.php?controller=CrashTest&action=index");
        exit;
    }

    public function devStressTest(): void
    {
        $this->requireAdmin();

        $appEnv = $_ENV['APP_ENV'] ?? 'prod';
        if ($appEnv !== 'dev') {
            $this->logCrash('Crash test: stress test refusé hors DEV', 'warn', [
                'app_env' => $appEnv,
            ]);

            $_SESSION['flash_error'] = "Le stress test est autorisé uniquement en mode DEV.";
            header("Location: index.php?controller=CrashTest&action=index");
            exit;
        }

        $iterations = 20000;
        $hash = '';
        $start = microtime(true);

        for ($i = 0; $i < $iterations; $i++) {
            $hash = hash('sha256', $hash . $i . random_int(1, 999999));
        }

        $duration = round(microtime(true) - $start, 4);

        $this->logCrash('Crash test: stress test DEV exécuté', 'warn', [
            'iterations' => $iterations,
            'duration_sec' => $duration,
            'hash_preview' => substr($hash, 0, 12),
        ]);

        $_SESSION['flash_success'] = "Stress test DEV terminé en {$duration} seconde(s).";
        header("Location: index.php?controller=CrashTest&action=index");
        exit;
    }

    public function logFloodTest(): void
    {
        $this->requireAdmin();

        $appEnv = $_ENV['APP_ENV'] ?? 'prod';
        if ($appEnv !== 'dev') {
            $_SESSION['flash_error'] = "Le flood de logs est autorisé uniquement en mode DEV.";
            header("Location: index.php?controller=CrashTest&action=index");
            exit;
        }

        $iterations = 30000;
        $payload = str_repeat('LOGFLOOD-', 512); // ~4 Ko par log
        $start = microtime(true);

        for ($i = 1; $i <= $iterations; $i++) {
            if (function_exists('log_console')) {
                log_console('LOG_FLOOD_TEST', 'warn', [
                    'iteration' => $i,
                    'payload' => $payload,
                    'mode' => 'dev_stress_log',
                ]);
            }
        }

        $duration = round(microtime(true) - $start, 4);

        if (function_exists('log_console')) {
            log_console('Flood de logs terminé', 'warn', [
                'iterations' => $iterations,
                'approx_payload_kb_per_log' => 4,
                'duration_sec' => $duration,
            ]);
        }

        $_SESSION['flash_success'] = "Flood de logs DEV terminé en {$duration} seconde(s).";
        header("Location: index.php?controller=CrashTest&action=index");
        exit;
    }
}
