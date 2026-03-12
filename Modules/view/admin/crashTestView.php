<?php

?>
<main class="app">
    <section class="page">
        <div class="center">
            <h1 class="heading">Crash Test Admin</h1>

            <p class="lead">
                Cette page permet de déclencher volontairement plusieurs situations d’erreur
                pour tester les handlers, l’affichage DEV/PROD, les logs et la request id.
            </p>

            <div class="card crash-card-container">

                <div>
                    <a class="btn active btn-but"
                       href="index.php?controller=CrashTest&action=throwException">
                        Tester erreur 500 par exception
                    </a>
                    <p class="lead">
                        Déclenche une exception PHP volontaire. Sert à tester le catch global, la page 500
                        et l’affichage différent entre DEV et PROD.
                    </p>
                </div>

                <div>
                    <a class="btn active btn-but"
                       href="index.php?controller=CrashTest&action=callUndefinedFunction">
                        Tester fonction inexistante
                    </a>
                    <p class="lead">
                        Appelle une fonction qui n’existe pas. Produit une erreur fatale utile pour vérifier
                        la capture des erreurs lourdes et les logs système.
                    </p>
                </div>

                <div>
                    <a class="btn active btn-but"
                       href="index.php?controller=CrashTest&action=callMethodOnNull">
                        Tester méthode sur null
                    </a>
                    <p class="lead">
                        Tente d’appeler une méthode sur une variable nulle. Permet de tester un crash PHP classique
                        lié à une mauvaise référence objet.
                    </p>
                </div>

                <div>
                    <a class="btn active btn-but"
                       href="index.php?controller=CrashTest&action=invalidView">
                        Tester vue inexistante
                    </a>
                    <p class="lead">
                        Demande l’affichage d’une vue absente. Sert à tester la robustesse du ViewHandler
                        et la gestion des erreurs liées au rendu.
                    </p>
                </div>

                <div>
                    <a class="btn active btn-but"
                       href="index.php?controller=CrashTest&action=invalidControllerTarget">
                        Tester contrôleur / action inexistants
                    </a>
                    <p class="lead">
                        Envoie vers une route invalide. Très utile pour tester la gestion des contrôleurs absents,
                        des actions fantômes et les routes cassées.
                    </p>
                </div>

                <div>
                    <a class="btn active btn-but"
                       href="index.php?controller=CrashTest&action=dbFailure">
                        Tester erreur SQL volontaire
                    </a>
                    <p class="lead">
                        Lance une requête vers une table inexistante. Permet de vérifier les erreurs PDO,
                        les logs techniques et la réaction du système en cas de problème base de données.
                    </p>
                </div>

                <div>
                    <a class="btn active btn-but"
                       href="index.php?controller=CrashTest&action=userError">
                        Tester trigger_error fatal
                    </a>
                    <p class="lead">
                        Déclenche un <code>E_USER_ERROR</code> volontaire. Sert à tester les handlers PHP
                        et les erreurs fatales déclenchées manuellement.
                    </p>
                </div>

                <div>
                    <a class="btn active btn-but"
                       href="index.php?controller=CrashTest&action=typeError">
                        Tester TypeError volontaire
                    </a>
                    <p class="lead">
                        Passe un mauvais type à une méthode strictement typée. Très utile pour vérifier
                        le comportement avec <code>strict_types</code> et les erreurs de typage.
                    </p>
                </div>

                <div>
                    <a class="btn active btn-but"
                       href="index.php?controller=CrashTest&action=test404">
                        Tester erreur 404
                    </a>
                    <p class="lead">
                        Force une réponse HTTP 404. Sert à tester la gestion des pages introuvables
                        et la différence entre erreur d’application et page absente.
                    </p>
                </div>

                <div>
                    <a class="btn active btn-but"
                       href="index.php?controller=CrashTest&action=brokenRedirect">
                        Tester redirection cassée
                    </a>
                    <p class="lead">
                        Redirige volontairement vers une URL inexistante. Utile pour vérifier le comportement
                        des redirections invalides et les routes finales non résolues.
                    </p>
                </div>

                <div>
                    <a class="btn active btn-but"
                       href="index.php?controller=CrashTest&action=bruteForceTest">
                        Tester brute force login
                    </a>
                    <p class="lead">
                        Simule plusieurs tentatives de connexion échouées sur un même compte et la même IP.
                        Permet de vérifier les blocages progressifs, les logs sécurité et la protection anti
                        brute force.
                    </p>
                </div>

                <div>
                    <a class="btn active btn-but"
                       href="index.php?controller=CrashTest&action=devStressTest">
                        Tester charge contrôlée (DEV)
                    </a>
                    <p class="lead">
                        Lance une boucle de calcul intensive uniquement en mode DEV. Sert à faire un mini stress test
                        local sans attaquer réellement le serveur ni risquer la production.
                    </p>
                </div>

            </div>
        </div>
    </section>
</main>
