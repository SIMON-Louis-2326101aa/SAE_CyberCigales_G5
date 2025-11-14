<?php

namespace SAE_CyberCigales_G5\Modules\model;

class ButterflyWayModel
{
    private array $path = ['R','L','L','R','R','L','R','L','R','R']; // 10 étapes

    private array $storyHints = [
        // 0..9 : indices énigmatiques
        0 => "Un souffle tiède longe le mur éclairé — là où la vitrine chuchote. (droite)",
        1 => "La poussière danse vers l’affiche déchirée, à l’opposé des néons. (gauche)",
        2 => "L’odeur de bois humide vient de l’étagère sans étiquettes. (gauche)",
        3 => "Un tintement, bref, métallique, résonne près de la caisse. (droite)",
        4 => "La lumière vacille, puis se fixe près des boîtes empilées. (droite)",
        5 => "Un courant d’air passe par la porte de service entrouverte. (gauche)",
        6 => "Un pas léger — pas le tien — s’arrête derrière le présentoir. (droite)",
        7 => "Le papillon se pose sur un panneau renversé, côté sombre. (gauche)",
        8 => "Des griffures minuscules indiquent la voie des registres. (droite)",
        9 => "Le silence se contracte vers la réserve, comme un dernier aveu. (droite)",
    ];

    public function state(): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['pap_started']  = $_SESSION['pap_started']  ?? false;
        $_SESSION['pap_step']     = $_SESSION['pap_step']     ?? 0;      // 0..10
        $_SESSION['pap_score']    = $_SESSION['pap_score']    ?? 0;      // -1..10
        $_SESSION['pap_feedback'] = $_SESSION['pap_feedback'] ?? null;
        $_SESSION['pap_code_ok']  = $_SESSION['pap_code_ok']  ?? false;

        $step = (int) $_SESSION['pap_step'];

        return [
            'started'   => (bool) $_SESSION['pap_started'],
            'step'      => $step,
            'maxSteps'  => 10,
            'score'     => (int) $_SESSION['pap_score'],
            'feedback'  => $_SESSION['pap_feedback'],
            'code_ok'   => (bool) $_SESSION['pap_code_ok'],
            'hint'      => $step < 10 ? ($this->storyHints[$step] ?? '') :
                "Tu sens qu’il n’y a plus rien devant. Il ne reste qu’à… **se retourner**.",
        ];
    }

    public function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['pap_started']  = true;
        $_SESSION['pap_step']     = 0;
        $_SESSION['pap_score']    = 0;
        $_SESSION['pap_feedback'] = "Suis les indices. Une erreur te colle à -1… jusqu’à ce que tu te retournes.";
        $_SESSION['pap_code_ok']  = false;
    }

    public function move(string $dir): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION['pap_started'])) {
            return;
        }

        $step = (int) $_SESSION['pap_step'];
        if ($step >= 10) {
            return; // déjà au bout
        }

        $expected = $this->path[$step] ?? 'R';
        $dir = strtoupper($dir) === 'L' ? 'L' : 'R';

        if ($dir === $expected) {
            if ((int)$_SESSION['pap_score'] >= 0) {
                $_SESSION['pap_score'] = (int)$_SESSION['pap_score'] + 1;
            }
            $_SESSION['pap_feedback'] = "Juste. Les ailes vibrent d’accord… (+1)";
        } else {
            $_SESSION['pap_score'] = -1; // reste bloqué à -1 jusqu’à “se retourner”
            $_SESSION['pap_feedback'] = "Faux chemin. Tu es collé à -1 jusqu’à ce que tu te retournes.";
        }

        $_SESSION['pap_step'] = $step + 1; // on avance toujours dans le couloir
    }

    public function turnBack(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $step = (int) $_SESSION['pap_step'];

        if ($step < 10) {
            // reset du -1 → 0 uniquement si on s'était trompé
            if ((int)$_SESSION['pap_score'] === -1) {
                $_SESSION['pap_score'] = 0;
                $_SESSION['pap_feedback'] = "Tu te retournes. Le poids tombe : le score revient à 0.";
            } else {
                $_SESSION['pap_feedback'] = "Tu te retournes, mais rien ne change.";
            }
        } else {
            // à la dernière “page” : afficher la page code
            $_SESSION['pap_feedback'] = "Derrière toi, une étiquette… Entrer le code pour ouvrir.";
        }
    }

    public function submitCode(string $code): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $ok = (mb_strtolower(trim($code), 'UTF-8') === 'papillon' || mb_strtolower(trim($code), 'UTF-8') ===
            'nollipap');
        $_SESSION['pap_code_ok'] = $ok;
        $_SESSION['pap_feedback'] = $ok ? "…le papillon approuve. L’épreuve est close."
            : "Le cadenas reste froid. Mauvais code.";
    }
}
