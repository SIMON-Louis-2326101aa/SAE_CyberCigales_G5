<?php

/**
 * Class pageModel
 * Modèle lié à la page d'accueil (ou pages générales).
 * Hérite de `database` pour récupérer la connexion PDO unique du projet.
 */

declare(strict_types=1);

namespace SAE_CyberCigales_G5\Module\model;

class PageModel extends Database
{
    /** Connexion PDO locale pour ce modèle. */
    private ?PDO $db = null;

    /**
     * Constructeur
     * - Récupère la connexion PDO via la classe parente `database`.
     * - Journalise l'initialisation pour le debug en dev.
     */
    public function __construct()
    {
        try {
            // Appelle la méthode héritée pour obtenir la connexion à la base.
            $this->db = $this->getBdd();

            if (function_exists('log_console')) {
                log_console('pageModel initialisé : PDO prêt', 'ok'); // ✅
            }
        } catch (Throwable $e) {
            if (function_exists('log_console')) {
                log_console('pageModel : échec initialisation PDO - ' . $e->getMessage(), 'error'); // ❌
            }
            // On relance une exception plus neutre pour le haut de pile.
            throw new RuntimeException("Impossible d'initialiser pageModel.");
        }
    }
}
