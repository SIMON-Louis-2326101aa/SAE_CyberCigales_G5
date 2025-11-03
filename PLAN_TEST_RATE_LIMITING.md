# 🧪 Plan de Test - Rate Limiting

## 📋 Prérequis

- ✅ Vous êtes sur la branche `feature/connection-rate-limiting`
- ✅ Votre serveur local est démarré (XAMPP, WAMP, ou autre)
- ✅ La base de données est configurée
- ✅ Vous avez un compte utilisateur de test

---

## 🎯 Tests à Effectuer

### Test 1 : Tentatives Progressives (Compteur)

#### Objectif
Vérifier que le système compte les tentatives et affiche le nombre restant.

#### Étapes
1. Allez sur la page de connexion : `http://localhost/votre-projet/public/index.php?controller=redirection&action=openFormConnection`
2. Entrez un **email valide** mais un **mauvais mot de passe**
3. Cliquez sur "Se connecter"
4. ✅ **Résultat attendu** : Message "Il vous reste 4 tentative(s) avant le blocage temporaire"
5. Répétez avec le même email + mauvais mot de passe
6. ✅ **Tentative 2** : "Il vous reste 3 tentative(s)..."
7. ✅ **Tentative 3** : "Il vous reste 2 tentative(s)..."
8. ✅ **Tentative 4** : "Il vous reste 1 tentative(s)..."

---

### Test 2 : Blocage Temporaire (Email)

#### Objectif
Vérifier que le compte est bloqué après 5 tentatives.

#### Étapes
1. Continuez du Test 1
2. Faites une **5ème tentative** avec le même email + mauvais mot de passe
3. ✅ **Résultat attendu** : Message "Trop de tentatives de connexion échouées. Votre compte est temporairement bloqué. Veuillez réessayer dans 15 minute(s)."
4. Essayez de vous connecter à nouveau (même avec le bon mot de passe)
5. ✅ **Résultat attendu** : Le compte reste bloqué

#### Vérification
- [ ] Le message de blocage s'affiche
- [ ] Le nombre de minutes est affiché (15 ou moins)
- [ ] Impossible de se connecter même avec le bon mot de passe

---

### Test 3 : Remise à Zéro après Connexion Réussie

#### Objectif
Vérifier que le compteur est remis à zéro après une connexion réussie.

#### Étapes
1. Faites **2 tentatives échouées** avec un email + mauvais mot de passe
2. ✅ Devrait afficher : "Il vous reste 3 tentative(s)..."
3. À la **3ème tentative**, entrez le **bon mot de passe**
4. ✅ **Résultat attendu** : Connexion réussie
5. Déconnectez-vous
6. Refaites une tentative échouée
7. ✅ **Résultat attendu** : "Il vous reste 4 tentative(s)..." (compteur remis à 0)

#### Vérification
- [ ] La connexion réussie efface les tentatives précédentes
- [ ] Le compteur repart de 5 après déconnexion

---

### Test 4 : Limitation par IP

#### Objectif
Vérifier que l'IP est bloquée après trop de tentatives (différents emails).

#### Étapes
1. Faites **3 tentatives échouées** avec l'email `test1@example.com`
2. Faites **3 tentatives échouées** avec l'email `test2@example.com`
3. Faites **4 tentatives échouées** avec l'email `test3@example.com`
4. ✅ **Résultat attendu** : Message "Trop de tentatives de connexion depuis cette adresse IP. Veuillez réessayer dans 30 minute(s)."

#### Vérification
- [ ] Après 10 tentatives (tous emails confondus), l'IP est bloquée
- [ ] Le message indique 30 minutes
- [ ] Même avec un email jamais utilisé, l'IP reste bloquée

---

### Test 5 : Déblocage Automatique

#### Objectif
Vérifier que le blocage se lève automatiquement après 15 minutes.

#### Étapes
**⚠️ Ce test est long ! Vous pouvez le faire en dernier.**

1. Faites 5 tentatives échouées pour être bloqué
2. ✅ Vérifiez le blocage
3. **Option A** : Attendez 15 minutes (☕ pause café)
4. **Option B** : Modifiez temporairement `BLOCK_DURATION` dans `loginAttemptModel.php` à 1 minute pour tester plus vite
5. Après le délai, essayez de vous connecter
6. ✅ **Résultat attendu** : Le compte est débloqué

#### Vérification
- [ ] Après 15 minutes (ou le délai modifié), connexion possible
- [ ] Le compteur repart de 0

---

### Test 6 : Messages d'Erreur Corrects

#### Objectif
Vérifier que les messages sont clairs et informatifs.

#### Étapes
1. Faites des tentatives échouées
2. Vérifiez les messages affichés

#### Messages attendus
- [ ] "Il vous reste X tentative(s) avant le blocage temporaire"
- [ ] "Trop de tentatives... bloqué. Veuillez réessayer dans X minute(s)."
- [ ] Les minutes restantes sont affichées correctement
- [ ] Les messages sont en français
- [ ] Pas d'erreurs PHP affichées

---

### Test 7 : Nettoyage Automatique

#### Objectif
Vérifier que les tentatives anciennes sont nettoyées.

#### Étapes
1. Faites **2 tentatives échouées**
2. ✅ Devrait afficher : "Il vous reste 3 tentative(s)..."
3. Attendez **15 minutes** (ou modifiez `BLOCK_DURATION` à 1 minute)
4. Faites une nouvelle tentative échouée
5. ✅ **Résultat attendu** : "Il vous reste 4 tentative(s)..." (les anciennes ont été nettoyées)

#### Vérification
- [ ] Les tentatives de plus de 15 minutes sont automatiquement supprimées
- [ ] Le compteur reflète uniquement les tentatives récentes

---

## 🔧 Modification pour Tests Rapides

Pour tester plus rapidement (sans attendre 15 minutes), vous pouvez temporairement modifier :

**Fichier :** `Modules/model/loginAttemptModel.php`

```php
// Ligne 11-12 - Modifier temporairement pour les tests
private const MAX_ATTEMPTS = 5; // ou 3 pour tester plus vite
private const BLOCK_DURATION = 1; // 1 minute au lieu de 15
```

**⚠️ Important :** Remettez les valeurs originales après les tests !

---

## 📊 Checklist Complète

### Fonctionnalités de base
- [ ] Le compteur de tentatives fonctionne
- [ ] Le message "Il vous reste X tentative(s)" s'affiche
- [ ] Le blocage se déclenche après 5 tentatives
- [ ] Le message de blocage indique le temps restant
- [ ] La connexion réussie remet le compteur à 0

### Sécurité
- [ ] Impossible de se connecter pendant le blocage (même avec bon mot de passe)
- [ ] Le blocage par IP fonctionne (10 tentatives max)
- [ ] Les tentatives sont comptées par email ET par IP
- [ ] Pas d'erreur PHP visible

### Nettoyage & Performance
- [ ] Les tentatives anciennes sont nettoyées automatiquement
- [ ] Le déblocage automatique fonctionne après 15 minutes
- [ ] Pas de ralentissement visible lors de la connexion

### Interface Utilisateur
- [ ] Les messages sont clairs et en français
- [ ] Le temps restant est affiché en minutes
- [ ] Pas de crash ou d'erreur 500

---

## 🐛 Problèmes Possibles

### Problème 1 : Les tentatives ne sont pas comptées
**Cause possible :** Sessions PHP non démarrées  
**Solution :** Vérifier que `session_start()` est appelé dans `public/index.php`

### Problème 2 : Le blocage ne fonctionne pas
**Cause possible :** Le code n'est pas exécuté  
**Solution :** Vérifier que `userController.php` utilise bien `loginAttemptModel`

### Problème 3 : Le temps restant est incorrect
**Cause possible :** Calcul du temps en secondes au lieu de minutes  
**Solution :** Vérifier la ligne `ceil($remainingTime / 60)` dans le contrôleur

### Problème 4 : Le déblocage ne fonctionne pas
**Cause possible :** Le nettoyage automatique ne s'exécute pas  
**Solution :** Vérifier la méthode `cleanOldAttempts()` dans le model

---

## ✅ Validation Finale

Une fois tous les tests passés, vous pouvez :
1. ✅ Remettre les valeurs originales (5 tentatives, 15 minutes)
2. ✅ Faire un dernier test complet
3. ✅ Commit les éventuelles corrections
4. ✅ Merger dans `main` ou créer une Pull Request

---

## 📝 Notes de Test

Utilisez cet espace pour noter vos observations :

**Test 1 - Compteur :**
- [ ] ✅ Réussi
- [ ] ❌ Problème : ___________________________

**Test 2 - Blocage Email :**
- [ ] ✅ Réussi
- [ ] ❌ Problème : ___________________________

**Test 3 - Remise à Zéro :**
- [ ] ✅ Réussi
- [ ] ❌ Problème : ___________________________

**Test 4 - Blocage IP :**
- [ ] ✅ Réussi
- [ ] ❌ Problème : ___________________________

**Test 5 - Déblocage Auto :**
- [ ] ✅ Réussi
- [ ] ❌ Problème : ___________________________

**Test 6 - Messages :**
- [ ] ✅ Réussi
- [ ] ❌ Problème : ___________________________

**Test 7 - Nettoyage :**
- [ ] ✅ Réussi
- [ ] ❌ Problème : ___________________________

---

**Bon courage pour les tests ! 🧪**

Si vous rencontrez un problème, notez-le et je pourrai vous aider à le corriger ! 💪


