<?php
/**
 * Vue "À propos" - Page de présentation du projet et de l'équipe
 * 
 * Cette vue présente :
 * - L'équipe CyberCigales G5
 * - Le projet SAE
 * - Les technologies utilisées
 * - Les objectifs pédagogiques
 * 
 * @author SAE CyberCigales G5
 * @version 1.0
 */
?>

<!-- Page À propos - Présentation du projet CyberCigales -->
<div id="about-page">
    <h1>À Propos de CyberCigales</h1>
    
    <!-- Section Équipe -->
    <section class="team-section">
        <h2>Notre Équipe</h2>
        <p>Nous sommes un groupe d'étudiants de l'Université d'Aix-Marseille, passionnés par la cybersécurité et le développement web.</p>
        
        <div class="team-grid">
            <div class="team-member">
                <h3>BADJOUDJ Hana</h3>
                <p>Développeuse Frontend & Sécurité</p>
            </div>
            
            <div class="team-member">
                <h3>CONTRUCCI Lou</h3>
                <p>Développeuse Backend & Base de données</p>
            </div>
            
            <div class="team-member">
                <h3>DIAZ Gwenn</h3>
                <p>Développeuse Full-Stack & UI/UX</p>
            </div>
            
            <div class="team-member">
                <h3>GUZELBABA Imran</h3>
                <p>Développeur Backend & Sécurité</p>
            </div>
            
            <div class="team-member">
                <h3>SIMON Louis</h3>
                <p>Développeur Full-Stack & DevOps</p>
            </div>
        </div>
    </section>

    <!-- Section Projet -->
    <section class="project-section">
        <h2>Le Projet</h2>
        <p><strong>CyberCigales</strong> est un projet universitaire réalisé dans le cadre de la SAE (Situation d'Apprentissage et d'Évaluation) à l'Université d'Aix-Marseille.</p>
        
        <h3>Objectifs Pédagogiques</h3>
        <ul>
            <li>Maîtrise du développement web en PHP</li>
            <li>Implémentation de l'architecture MVC</li>
            <li>Gestion sécurisée des utilisateurs</li>
            <li>Pratique du travail en équipe</li>
            <li>Développement de bonnes pratiques de sécurité</li>
        </ul>
        
        <h3>Fonctionnalités Principales</h3>
        <ul>
            <li>✅ <strong>Authentification sécurisée</strong> - Connexion/Déconnexion/Inscription</li>
            <li>✅ <strong>Gestion des sessions</strong> - Protection des données utilisateur</li>
            <li>✅ <strong>Interface modulaire</strong> - Architecture MVC propre</li>
            <li>✅ <strong>Récupération de mot de passe</strong> - Système d'email sécurisé</li>
            <li>✅ <strong>Pages informatives</strong> - Accueil, Mentions légales, À propos</li>
            <li>✅ <strong>Documentation complète</strong> - Code commenté et structuré</li>
        </ul>
    </section>

    <!-- Section Technologies -->
    <section class="tech-section">
        <h2>Technologies Utilisées</h2>
        
        <div class="tech-grid">
            <div class="tech-category">
                <h3>Backend</h3>
                <ul>
                    <li>PHP 8.0+ (Native)</li>
                    <li>MySQL (Base de données)</li>
                    <li>PDO (Sécurité SQL)</li>
                    <li>Sessions PHP</li>
                </ul>
            </div>
            
            <div class="tech-category">
                <h3>Frontend</h3>
                <ul>
                    <li>HTML5 (Sémantique)</li>
                    <li>CSS3 (Responsive)</li>
                    <li>JavaScript (Interactions)</li>
                    <li>Design Responsive</li>
                </ul>
            </div>
            
            <div class="tech-category">
                <h3>Architecture</h3>
                <ul>
                    <li>MVC (Model-View-Controller)</li>
                    <li>Autoloading PHP</li>
                    <li>Routing personnalisé</li>
                    <li>Séparation des responsabilités</li>
                </ul>
            </div>
            
            <div class="tech-category">
                <h3>Sécurité</h3>
                <ul>
                    <li>Hashage des mots de passe</li>
                    <li>Protection XSS</li>
                    <li>Validation des données</li>
                    <li>Sessions sécurisées</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Section Contexte -->
    <section class="context-section">
        <h2>Contexte Universitaire</h2>
        <p>Ce projet s'inscrit dans le cadre de notre formation en informatique à l'<strong>Université d'Aix-Marseille</strong>. Il nous permet de mettre en pratique les concepts théoriques appris en cours :</p>
        
        <ul>
            <li><strong>Développement Web</strong> - Maîtrise des technologies web modernes</li>
            <li><strong>Cybersécurité</strong> - Application des bonnes pratiques de sécurité</li>
            <li><strong>Architecture Logicielle</strong> - Implémentation du pattern MVC</li>
            <li><strong>Travail d'Équipe</strong> - Collaboration et gestion de projet</li>
            <li><strong>Documentation</strong> - Rédaction de code propre et commenté</li>
        </ul>
    </section>

    <!-- Section Contact -->
    <section class="contact-section">
        <h2>Contact</h2>
        <p>Pour toute question concernant ce projet, vous pouvez nous contacter :</p>
        <p><strong>Équipe CyberCigales G5</strong><br>
        Université d'Aix-Marseille<br>
        Projet SAE - Cybersécurité</p>
    </section>

    <!-- Section Remerciements -->
    <section class="thanks-section">
        <h2>Remerciements</h2>
        <p>Nous remercions nos enseignants pour leur accompagnement et leurs conseils précieux dans la réalisation de ce projet.</p>
    </section>
</div>
