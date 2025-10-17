<?php
require_once __DIR__ . '/../model/formRegisterModel.php';
require_once __DIR__ . '/../../includes/viewHandler.php';

//require __DIR__ . '/../view/formRegisterView.php';
class formRegisterController
{
    private $formInscriptionModel;
    public function __construct() {
        $this->formInscriptionModel = new formRegisterModel();
    }

    public function register() {
        if (isset($_POST['register'])) {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['pwd'] ?? '';
            $confirm = $_POST['confirm_pwd'] ?? '';

            if(strlen($password) < 8) {
                $error = "Votre mot de passe n'est pas assez long : minimuhn 8 caractères";
                viewHandler::show("../view/formRegisterView");
                echo $error;
                return;
            }

            $verif_majuscule = '/[A-Z]/'; // Au moins une majuscule
            $verif_minuscule = '/[a-z]/'; // Au moins une minuscule
            $verif_chiffre = '/[0-9]/';   // Au moins un chiffre
            $verif_special = '/[^a-zA-Z0-9]/'; // Au moins un caractère spécial (non alpha-numérique)

            if (!preg_match($verif_majuscule, $password) ||
                !preg_match($verif_minuscule, $password) ||
                !preg_match($verif_chiffre, $password) ||
                !preg_match($verif_special, $password))
            {
                $error = "Le mot de passe doit contenir au moins : 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
                viewHandler::show("../view/formRegisterView");
                echo $error;
                return;
            }

            if ($password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas.";
                viewHandler::show("../view/formRegisterView");
                echo $error;
                return;
            }

            if ($this->formInscriptionModel->findByEmail($email)) {
                $error = "Impossible de créer le compte. Veuillez vérifier les informations saisies.";
                viewHandler::show("../view/formRegisterView");
                echo $error;
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
                    $_SESSION['user_id'] = $utilisateur['id'];
                    $_SESSION['nom'] = $utilisateur['nom'];
                    $_SESSION['prenom'] = $utilisateur['prenom'];
                    $_SESSION['email'] = $utilisateur['email'];

                    header("Location: /index.php?controller=homepage&action=openHomepage");
                    exit();
                } else {
                    // Fallback : utilisateur non retrouvé
                    $error = "Inscription réussie, mais problème de connexion automatique.";
                    ViewHandler::show('../view/formConnectionView', ['error' => $error]);
                    echo $error;
                    return;
                }
            } else {
                $error = "Erreur lors de l'inscription.";
                ViewHandler::show('../view/formRegisterView', ['error' => $error]);
                echo $error;
                return;
            }
        }
        viewHandler::show("../view/formRegisterView");

    }
}
