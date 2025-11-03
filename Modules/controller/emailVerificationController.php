<?php
require_once __DIR__ . '/../model/emailVerificationModel.php';
require_once __DIR__ . '/../../includes/mailer.php';
require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../../includes/ViewHandler.php';

class emailVerificationController
{
    private $eModel;
    private $user ;

    public function __construct()
    {
        $this->eModel = new emailVerificationModel();
        $this->user = new userModel();
    }

    public function request()
    {
        $email = $_GET['email'] ?? '';
        if (!$email) {
            $_SESSION['flash_error'] = "Adresse e-mail manquante.";
            header('Location: index.php?controller=redirection&action=openFormRegister');
            exit;
        }

        // Toujours regénérer un code frais pour éviter un délai expiré/perdu
        $code = $this->eModel->generateAndStoreCode($email);

        $subject = 'Vérification de votre adresse email';
        $message = '
<div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td align="center">
    <table width="600" style="background:#ffffff; padding:20px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
      <tr><td style="text-align:center;">
        <h2 style="color:#333333;">Vérification de votre adresse email</h2>
        <p style="font-size:16px; color:#555555;">Merci de vous être inscrit !</p>
        <p style="font-size:16px; color:#555555;">Votre code de vérification est :</p>
        <p style="font-size:24px; font-weight:bold; color:#007bff; background:#e9f7ff; padding:10px; border-radius:4px; display:inline-block;">' . htmlspecialchars($code) . '</p>
        <p style="font-size:14px; color:#888888;">Ce code expire dans 10 minutes.</p>
      </td></tr>
    </table>
  </td></tr></table>
</div>';
        $sent = Mailer::send($email, $subject, $message);

        // L'email doit être passé dans l'URL pour être récupéré par l'afficheur
        $url = 'Location: index.php?controller=redirection&action=openEmailVerification&email=' . urlencode($email);

        if ($sent) {
            $_SESSION['flash_success'] = "Un code vous a été envoyé.";
        } else {
            if (class_exists('Constant') && method_exists('Constant','isDev') && Constant::isDev()) {
                $_SESSION['flash_info'] = "Le mail n'a pas été envoyé. Code pour dev: {$code}";
            } else {
                $_SESSION['flash_error'] = "Erreur lors de l'envoi du code.";
            }
        }

        header($url); // Redirige vers le redirectionController
        exit;
    }

    public function verify()
    {
        $email = $_POST['email'] ?? '';
        $code  = $_POST['code']  ?? '';

        //Ajout de l'email à l'URL de redirection en cas d'erreur
        $errorRedirectUrl = 'Location: index.php?controller=redirection&action=openEmailVerification&email=' . urlencode($email);

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
                header('Location: index.php?controller=redirection&action=openFormConnection');
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
            ? "Code expiré (10 minutes). <a href=\"index.php?controller=emailVerification&action=request&email=" . urlencode($email) . "\">Renvoyer un code</a>."
            : "Code incorrect. Vérifiez et réessayez.";

        header($errorRedirectUrl); // Redirection après échec
        exit;
    }
}