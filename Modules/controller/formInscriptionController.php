<?php
require_once __DIR__ . '/../model/formInscriptionModel.php';
require_once __DIR__ . '/../../includes/viewHandler.php';

//require __DIR__ . '/../view/formInscriptionView.php';
class formInscriptionController
{
    private $formInscriptionModel;
    public function __construct() {
        $this->formInscriptionModel = new formInscriptionModel();
    }

    public function register() {
        if (isset($_POST['register'])) {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_mdp'] ?? '';

            if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($confirm)) {
                $error = "Tous les champs obligatoires doivent être remplis.";
                ViewHandler::show('../view/formInscriptionView', ['error' => $error]);
                return;
            }

            if ($password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas.";
                viewHandler::show("../view/formInscriptionView", ['error' => $error]); 
                return;
            }

            if ($this->formInscriptionModel->findByEmail($email)) {
                $error = "Cet email est déjà utilisé.";
                viewHandler::show("../view/formInscriptionView", ['error' => $error]);
                return;
            }

            $success = $this->formInscriptionModel->register($nom, $prenom, $email, $password);

            if ($success) {
                // Connexion automatique après inscription
                require_once(__DIR__ . '/../model/formConnectionModel.php');// Inclure le modèle si nécessaire
                $connexionModel = new formConnectionModel();
                $utilisateur = $connexionModel->authenticate($email, $password);

                if ($utilisateur) {
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start([
                            'use_strict_mode' => true,
                            'cookie_httponly' => true,
                            'cookie_secure' => true,
                            'cookie_samesite' => 'None'
                        ]);
                    }

                    $_SESSION['utilisateur'] = $utilisateur;
                    $_SESSION['nom'] = $utilisateur['Nom'];
                    $_SESSION['email'] = $utilisateur['email'];

                    header("Location: /index.php?controller=homepage&action=openHomepage");
                    exit();
                } else {
                    // Fallback : utilisateur non retrouvé
                    $error = "Inscription réussie, mais problème de connexion automatique.";
                    ViewHandler::show('../view/formConnectionView', ['error' => $error]);
                    return;
                }
            } else {
                $error = "Erreur lors de l'inscription.";
                ViewHandler::show('../view/formInscriptionView', ['error' => $error]);
                return;
            }
        }
        viewHandler::show("../view/formInscriptionView");

    }
}
