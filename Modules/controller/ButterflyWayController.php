<?php

declare(strict_types=1);

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\includes\ViewHandler;

class ButterflyWayController
{
    /* =============== CONFIGURATION ======================== */

    /** Chemin attendu (L / R) */
    private array $path = ['L','R','L','L','R','R','L','R','L','L']; // 10 étapes

    /** Indices narratifs — PISTE DU PAPILLON */
    private array $storyHints = [
        0 => "Le sol semble plus sûr sur la gauche.",
        1 => "Un courant d’air vient de la droite.",
        2 => "Des traces anciennes bifurquent à gauche.",
        3 => "Un murmure persiste côté gauche.",
        4 => "La lumière vacille à droite.",
        5 => "Le silence est plus lourd à droite.",
        6 => "Une odeur de pierre humide à gauche.",
        7 => "Un pas résonne à droite.",
        8 => "Un symbole gravé pointe à gauche.",
        9 => "La sortie est proche. Ne doute plus.",
    ];

    private array $lostMessages = [
        "Le papillon disparaît d’un coup. Tu l’as perdu…",
        "Rien. Plus aucune aile, plus aucun signe.",
        "Tu tournes la tête une seconde… et il n’est plus là.",
        "Un frisson. Le silence. Tu n’es plus sûr du chemin.",
        "Un faux reflet t’a piégé. Il s’est volatilisé.",
    ];

    private function randomLostMessage(): string
    {
        try {
            return $this->lostMessages[random_int(0, count($this->lostMessages) - 1)];
        } catch (\Throwable) {
            return $this->lostMessages[0];
        }
    }

    /* ================== RENDER ============================ */

    private function render(array $data): void
    {
        ViewHandler::show('butterflyWayView', [
            'pageTitle' => 'La piste du papillon',
            'data'      => $data
        ]);
    }

    /* ================== PAGES (GET) ======================= */

    public function home(): void
    {
        $this->render($this->state());
    }

    public function code(): void
    {
        $this->render($this->state());
    }

    /* ================== ACTIONS (POST) =================== */

    public function start(): void
    {
        $this->reset();
        header('Location: index.php?controller=ButterflyWay&action=home');
        exit;
    }

    public function left(): void
    {
        $this->move('L');
        header('Location: index.php?controller=ButterflyWay&action=home');
        exit;
    }

    public function right(): void
    {
        $this->move('R');
        header('Location: index.php?controller=ButterflyWay&action=home');
        exit;
    }

    public function turn(): void
    {
        $this->turnBack();

        $next = ($_SESSION['pap_step'] ?? 0) >= count($this->path)
            ? 'code'
            : 'home';

        header('Location: index.php?controller=ButterflyWay&action=' . $next);
        exit;
    }

    public function submitCode(): void
    {
        $this->submitCodeValid($_POST['code'] ?? '');
        header('Location: index.php?controller=ButterflyWay&action=code');
        exit;
    }

    /* ================== LOGIQUE ========================== */

    private function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['pap_started']  = $_SESSION['pap_started']  ?? false;
        $_SESSION['pap_step']     = $_SESSION['pap_step']     ?? 0;
        $_SESSION['pap_score']    = $_SESSION['pap_score']    ?? 0;
        $_SESSION['pap_feedback'] = $_SESSION['pap_feedback'] ?? null;
        $_SESSION['pap_code_ok']  = $_SESSION['pap_code_ok']  ?? false;
    }

    private function state(): array
    {
        $this->ensureSession();

        $step = (int)$_SESSION['pap_step'];
        $max  = count($this->path);

        return [
            'started'  => (bool)$_SESSION['pap_started'],
            'step'     => $step,
            'maxSteps' => $max,
            'score'    => (int)$_SESSION['pap_score'],
            'feedback' => $_SESSION['pap_feedback'],
            'code_ok'  => (bool)$_SESSION['pap_code_ok'],
            'hint'     => $step < $max
                ? ($this->storyHints[$step] ?? '')
                : "Le papillon s’est posé. Il ne reste qu’un mot à murmurer.",
        ];
    }

    private function reset(): void
    {
        $this->ensureSession();

        $_SESSION['pap_started']  = true;
        $_SESSION['pap_step']     = 0;
        $_SESSION['pap_score']    = 0;
        $_SESSION['pap_feedback'] = "Suis le papillon. Une erreur te fera perdre sa trace.";
        $_SESSION['pap_code_ok']  = false;
    }

    /**
     * Règles :
     * - Si score = -1 => étape BLOQUÉE (on n'avance pas)
     * - Si mauvais choix => score = -1 + message aléatoire, étape ne bouge pas
     * - Si bon choix => step +1, score +1 (si score >= 0)
     */
    private function move(string $dir): void
    {
        $this->ensureSession();

        if (empty($_SESSION['pap_started'])) {
            return;
        }

        $max  = count($this->path);
        $step = (int)$_SESSION['pap_step'];

        // Déjà terminé => rien à faire
        if ($step >= $max) {
            return;
        }

        // Si bloqué (score -1) => on empêche d'avancer
        if ((int)$_SESSION['pap_score'] === -1) {
            $_SESSION['pap_feedback'] = $this->randomLostMessage() . " (Reprends la trace.)";
            return;
        }

        $expected = $this->path[$step] ?? 'R';
        $dir = (strtoupper($dir) === 'L') ? 'L' : 'R';

        if ($dir === $expected) {
            $_SESSION['pap_score'] = (int)$_SESSION['pap_score'] + 1;
            $_SESSION['pap_step']  = $step + 1;
            $_SESSION['pap_feedback'] = "Tu suis sa trajectoire. (+1)";
        } else {
            // Mauvais => BLOQUE, et on n'avance pas d'étape
            $_SESSION['pap_score'] = -1;
            $_SESSION['pap_feedback'] = $this->randomLostMessage() . " (Score bloqué.)";
        }
    }

    /**
     * Règles :
     * - Si step < 10 => retour step 0 + score 0
     * - Si step == 10 => on reste sur step 10 et on affiche le code
     */
    private function turnBack(): void
    {
        $this->ensureSession();

        $max  = count($this->path);
        $step = (int)$_SESSION['pap_step'];

        if ($step < $max) {
            $_SESSION['pap_step']  = 0;
            $_SESSION['pap_score'] = 0;
            $_SESSION['pap_feedback'] = "Tu te retournes… mais étrangement tu te retrouve au point de départ.";
            return;
        }

        // step >= 10 : on est au bout, on laisse l'accès au code
        $_SESSION['pap_feedback'] = "Derrière toi, une étiquette… Le papillon attend un mot.";
    }


    private function submitCodeValid(string $code): void
    {
        $this->ensureSession();

        $val = mb_strtolower(trim($code), 'UTF-8');
        $ok  = in_array($val, ['papillon', 'nollipap'], true);

        $_SESSION['pap_code_ok']  = $ok;
        $_SESSION['pap_feedback'] = $ok
            ? "…le papillon approuve. L’épreuve est terminée."
            : "Rien ne se passe. Ce n’est pas le bon mot.";
    }
}
