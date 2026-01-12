<?php

declare(strict_types=1);

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\includes\ViewHandler;

class ButterflyWayController
{
    /* =============== CONFIGURATION ======================== */

    /** Chemin attendu (L / R / B pour la fin) */
    private array $path = ['L','R','L','L','R','R','L','R','L','L','B']; // 11 étapes

    /** Indices narratifs — PISTE DU PAPILLON */
    private array $storyHintsAlice = [
        0  => "Un log ancien subsiste. Peu visible, mais intact.",
        1  => "Une activité bruyante attire l’attention en surface.",
        2  => "Des métadonnées oubliées tracent un chemin flou.",
        3  => "Un signal faible persiste hors du flux principal.",
        4  => "Un accès trop exposé clignote inutilement.",
        5  => "Le silence apparent laisse transparaitre une trace plus discrète.",
        6  => "Une archive n’a jamais été correctement effacée.",
        7  => "Un trafic évident semble trop parfait.",
        8  => "Une signature ancienne a été laissée volontairement.",
        9  => "Les données se raréfient. Mais le sens est encore là.",
        10 => "Le papillon ralentit. Il attend que tu realises ce que d’autres ont ignoré.",
    ];

    private array $storyHintsBob = [
        0  => "Un point d’entrée discret semble moins exposé.",
        1  => "Un accès public attire trop facilement.",
        2  => "Une route secondaire contourne les contrôles visibles.",
        3  => "Un bruit parasite cache un chemin plus sûr.",
        4  => "Une interface trop lumineuse signale un risque.",
        5  => "Un silence artificiel est rarement rassurant.",
        6  => "Un accès interne n’a jamais été audité.",
        7  => "Un flux évident ressemble à un leurre.",
        8  => "Une validation ancienne n’a jamais été révoquée.",
        9  => "À ce stade, la solution simple est la plus dangereuse.",
        10 => "Le papillon change de logique. La sécurité exige parfois de reculer.",
    ];

    private array $lostMessages = [
        "Une alerte s’est déclenchée. Le signal a disparu.",
        "Trop direct. Le papillon s’est volatilisé.",
        "Une action brusque a effacé la trace.",
        "Avast a détécté une menace et t'a mis en quarantaine.",
        "Tu as attiré l’attention. Le système t'a redirigé.",
        "Erreur humaine détectée, tu a été déconnécté du système",
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
        $this->move('B');

        $max  = count($this->path);
        $step = (int)$_SESSION['pap_step'];

        // Si on est arrivé au bout (step == max) -> on révèle le code
        if ($step >= $max) {
            $_SESSION['pap_show_code'] = true;
            $_SESSION['pap_feedback']  = "Il ne reste plus qu’à valider l’accès.";
            header('Location: index.php?controller=ButterflyWay&action=code');
            exit;
        }

        // Sinon -> retour au début (et reset du blocage)
        $_SESSION['pap_step']      = 0;
        $_SESSION['pap_score']     = 0;
        $_SESSION['pap_show_code'] = false;
        $_SESSION['pap_feedback']  = "Tu te retournes… et tu reprends la piste depuis le début.";
        header('Location: index.php?controller=ButterflyWay&action=home');
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

        $team = $_SESSION['team'] ?? 'alice';

        $hints = match ($team) {
            'bob'   => $this->storyHintsBob,
            default => $this->storyHintsAlice,
        };

        return [
            'started'  => (bool)$_SESSION['pap_started'],
            'step'     => $step,
            'maxSteps' => $max,
            'score'    => (int)$_SESSION['pap_score'],
            'feedback' => $_SESSION['pap_feedback'],
            'code_ok'  => (bool)$_SESSION['pap_code_ok'],
            'hint'     => $step < $max
                ? ($hints[$step] ?? '')
                : "Il semble que tu aie trouvé ce que tu cherchais.",
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

        // Si on est déjà au max et qu'on "continue" => On se perd
        if ($step >= $max) {
            return;
        }

        // Si bloqué (score -1) => on empêche d'avancer
        if ((int)$_SESSION['pap_score'] === -1) {
            $_SESSION['pap_feedback'] = $this->randomLostMessage() . " (Reprends la trace.)";
            return;
        }

        $expected = $this->path[$step] ?? 'R';
        $dir = strtoupper($dir);
        if (!in_array($dir, ['L','R','B'], true)) {
            $dir = 'R';
        }

        if ($dir === $expected) {
            $_SESSION['pap_score'] = (int)$_SESSION['pap_score'] + 1;
            $_SESSION['pap_step']  = $step + 1;
            $_SESSION['pap_feedback'] = "Le signal s'amplifie.";
        } else {
            // Mauvais => BLOQUE, et on n'avance pas d'étape
            $_SESSION['pap_score'] = -1;
            $_SESSION['pap_feedback'] = $this->randomLostMessage() . " (Signal bloqué.)";
        }
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    private function submitCodeValid(string $code): void
    {
        $this->ensureSession();

        if (empty($_SESSION['team'])) {
            $_SESSION['flash_error'] = "Équipe inconnue.";
            $_SESSION['pap_code_ok'] = false;
            return;
        }

        $team = $_SESSION['team'];

        $waySolutions = [
            'alice' => ['admin'],
            'bob'   => ['root'],
        ];

        if (!isset($waySolutions[$team])) {
            $_SESSION['flash_error'] = "Équipe inconnue.";
            $_SESSION['pap_code_ok'] = false;
            return;
        }

        $val = $this->normalize($code);
        $allowed = array_merge($waySolutions[$team], ['nollipap']);
        $ok = in_array($val, $allowed, true);

        $_SESSION['pap_code_ok']  = $ok;
        if ($ok) {
            $_SESSION['pap_feedback'] = "Le terminal bip doucement. Accès accordé.";
            $_SESSION['flash_success'] = "AUTH OK — Le papillon reprend sa course. La suite s’ouvre.";
        } else {
            $_SESSION['pap_feedback'] = "Le terminal affiche : AUTH FAILED.";
            $_SESSION['flash_error'] = "Mot invalide — réessaie en suivant la logique de la piste.";
        }
    }
}
