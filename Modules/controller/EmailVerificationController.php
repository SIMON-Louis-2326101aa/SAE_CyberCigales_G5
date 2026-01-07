<?php

namespace SAE_CyberCigales\Modules\controller;

//require_once __DIR__ . '/../model/EmailVerificationModel.php';
//require_once __DIR__ . '/../../includes/Mailer.php';
//require_once __DIR__ . '/../model/UserModel.php';
//require_once __DIR__ . '/../../includes/ViewHandler.php';

use SAE_CyberCigales_G5\includes\Constant;
use SAE_CyberCigales_G5\includes\Mailer;
use SAE_CyberCigales_G5\Modules\model\EmailVerificationModel;
use SAE_CyberCigales_G5\Modules\model\UserModel;

/**
 * Contrôleur de vérification d'email
 * 
 * Gère l'envoi et la vérification des codes de vérification d'email
 * pour confirmer l'adresse email d'un utilisateur lors de l'inscription.
 * 
 * @package SAE_CyberCigales\Modules\controller
 * @author Équipe CyberCigales
 */
class EmailVerificationController
{
    /**
     * Instance du modèle de vérification d'email
     * 
     * @var EmailVerificationModel
     */
    private $eModel;
    
    /**
     * Instance du modèle utilisateur
     * 
     * @var UserModel
     */
    private $user;

    /**
     * Constructeur du contrôleur
     * 
     * Initialise les modèles nécessaires pour la vérification d'email.
     */
    public function __construct()
    {
        $this->eModel = new EmailVerificationModel();
        $this->user = new UserModel();
    }

    /**
     * Génère et envoie un code de vérification par email
     * 
     * Cette méthode génère un code de vérification à 6 chiffres,
     * le stocke en base de données et l'envoie par email à l'utilisateur.
     * En mode développement, le code est affiché dans un message flash.
     * 
     * @return void Redirige vers la page de saisie du code
     * 
     * @throws void Affiche un message d'erreur si l'email est manquant
     */
    public function request()
    {
        $email = $_GET['email'] ?? '';
        if (!$email) {
            $_SESSION['flash_error'] = "Adresse e-mail manquante.";
            header('Location: index.php?controller=Redirection&action=openFormRegister');
            exit;
        }

        // Toujours regénérer un code frais pour éviter un délai expiré/perdu
        $code = $this->eModel->generateAndStoreCode($email);

        $subject = 'Vérification de votre adresse email';
        $message = '
<div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td align="center">
    <table width="600" style="background:#ffffff; padding:20px; border-radius:8px; box-shadow:0 4px 8px 
    rgba(0,0,0,0.1);">
      <tr><td style="text-align:center;">
        <h2 style="color:#333333;">Vérification de votre adresse email</h2>
        <p style="font-size:16px; color:#555555;">Merci de vous être inscrit !</p>
        <p style="font-size:16px; color:#555555;">Votre code de vérification est :</p>
        <p style="font-size:24px; font-weight:bold; color:#007bff; background:#e9f7ff; 
        padding:10px; border-radius:4px; display:inline-block;">' . htmlspecialchars($code) . '</p>
        <p style="font-size:14px; color:#888888;">Ce code expire dans 10 minutes.</p>
      </td></tr>
    </table>
  </td></tr></table>
</div>';
        $sent = Mailer::send($email, $subject, $message);

        // L'email doit être passé dans l'URL pour être récupéré par l'afficheur
        $url = 'Location: index.php?controller=Redirection&action=openEmailVerification&email=' . urlencode($email);

        if ($sent) {
            $_SESSION['flash_success'] = "Un code vous a été envoyé.";
        } else {
            if (class_exists('Constant') && method_exists('Constant', 'isDev') && Constant::isDev()) {
                $_SESSION['flash_info'] = "Le mail n'a pas été envoyé. Code pour dev: {$code}";
            } else {
                $_SESSION['flash_error'] = "Erreur lors de l'envoi du code.";
            }
        }

        header($url); // Redirige vers le redirectionController
        exit;
    }

    /**
     * Vérifie le code de vérification saisi par l'utilisateur
     * 
     * Cette méthode vérifie que le code saisi correspond à celui envoyé par email,
     * qu'il n'est pas expiré (10 minutes) et crée le compte utilisateur si tout est valide.
     * 
     * Contrôles effectués :
     * - Présence de l'email et du code
     * - Format du code (6 chiffres)
     * - Validité et expiration du code
     * 
     * @return void Redirige vers la page de connexion en cas de succès,
     *              vers la page de saisie du code en cas d'échec
     * 
     * @uses EmailVerificationModel::checkCodeStatus() Pour vérifier le statut du code
     * @uses UserModel::createUserAfterVerification() Pour créer le compte après vérification
     */
    public function verify()
    {
        $email = $_POST['email'] ?? '';
        $code  = $_POST['code']  ?? '';

        //Ajout de l'email à l'URL de redirection en cas d'erreur
        $errorRedirectUrl = 'Location: index.php?controller=Redirection&action=openEmailVerification&email='
            . urlencode($email);

        // Si l'un des deux manque (email ou code)
        if (!$email || !$code) {
            $_SESSION['flash_error'] = "Veuillez saisir l'email et le code.";
            header($errorRedirectUrl); // Redirection après échec
            exit;
        }

        // Validation stricte: 6 chiffres
        if (!preg_match('/^[0-9]{6}$/', $code)) {
            $_SESSION['flash_error'] = "Format du code invalide (6 chiffres).";
            header($errorRedirectUrl); // Redirection après échec
            exit;
        }

        // Vérifier le statut détaillé du code
        $codeStatus = $this->eModel->checkCodeStatus($email, $code);

        if ($codeStatus['valid']) {
            // Créer le compte utilisateur maintenant que l'email est vérifié
            if ($this->user->createUserAfterVerification($email)) {
                // Succès : Redirection vers la page de connexion
                $_SESSION['flash_success'] = "Compte créé. Vous pouvez vous connecter.";
                header('Location: index.php?controller=Redirection&action=openFormConnection');
                exit;
            } else {
                // Erreur lors de la création du compte
                $_SESSION['flash_error'] = "Erreur lors de la création du compte. Réessayez.";
                header($errorRedirectUrl); // Redirection après échec
                exit;
            }
        }

        // Afficher un message d'erreur spécifique selon la raison
        $_SESSION['flash_error'] = ($codeStatus['reason'] === 'expired')
            ? "Code expiré (10 minutes). <a href=\"index.php?controller=emailVerification&action=request&email="
            . urlencode($email) . "\">Renvoyer un code</a>."
            : "Code incorrect. Vérifiez et réessayez.";

        header($errorRedirectUrl); // Redirection après échec
        exit;
    }
}
