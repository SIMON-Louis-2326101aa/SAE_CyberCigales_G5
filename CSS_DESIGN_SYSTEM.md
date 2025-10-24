# 🎨 Système de Design CSS - SAE CyberCigales

## 📋 Vue d'Ensemble

Ce fichier CSS moderne et professionnel offre un système de design complet pour le projet CyberCigales, avec un thème inspiré de la cybersécurité et de la cryptographie.

---

## ✨ Fonctionnalités Principales

### 🎨 **Variables CSS (Custom Properties)**
Toutes les couleurs, espacements, ombres et transitions sont définis en variables CSS pour faciliter la personnalisation.

### 🌈 **Palette de Couleurs**
- **Primaire** : Violet/Bleu (#667eea → #764ba2)
- **Accent** : Rose dégradé (#f093fb)
- **Status** : Success, Warning, Error, Info

### 📱 **Design Responsive**
- Desktop (>1024px)
- Tablet (768px - 1024px)
- Mobile (480px - 768px)
- Petit mobile (<480px)

### ✨ **Animations**
- Fade In / Fade In Up / Fade In Down
- Slide In
- Pulse
- Background animé

### 🌙 **Mode Sombre Automatique**
Détection automatique via `prefers-color-scheme`

---

## 🎯 Classes Utilitaires

### Boutons

```html
<!-- Bouton principal avec dégradé -->
<button class="btn btn-primary">Connexion</button>

<!-- Bouton secondaire -->
<button class="btn btn-secondary">Annuler</button>

<!-- Bouton de succès -->
<button class="btn btn-success">Valider</button>

<!-- Bouton de danger -->
<button class="btn btn-danger">Supprimer</button>

<!-- Tailles -->
<button class="btn btn-primary btn-lg">Grand</button>
<button class="btn btn-primary btn-sm">Petit</button>
```

### Cartes

```html
<div class="card">
    <div class="card-header">
        <h3>Titre de la carte</h3>
    </div>
    <div class="card-body">
        <p>Contenu de la carte</p>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary">Action</button>
    </div>
</div>
```

### Alertes

```html
<!-- Succès -->
<div class="alert alert-success">
    ✓ Opération réussie !
</div>

<!-- Erreur -->
<div class="alert alert-error">
    ✗ Une erreur s'est produite
</div>

<!-- Avertissement -->
<div class="alert alert-warning">
    ⚠ Attention requise
</div>

<!-- Information -->
<div class="alert alert-info">
    ℹ Information importante
</div>
```

### Badges

```html
<span class="badge badge-primary">Nouveau</span>
<span class="badge badge-success">Actif</span>
<span class="badge badge-warning">En attente</span>
<span class="badge badge-error">Erreur</span>
```

### Formulaires

```html
<div class="form-group">
    <label for="email">Email</label>
    <input type="email" id="email" placeholder="votre@email.com">
</div>

<div class="form-group">
    <label for="password">Mot de passe</label>
    <input type="password" id="password" placeholder="••••••••">
</div>

<button type="submit" class="btn btn-primary btn-lg">Se connecter</button>
```

### Espacements

```html
<!-- Marges -->
<div class="mt-1">Marge top petite</div>
<div class="mt-2">Marge top moyenne</div>
<div class="mt-3">Marge top grande</div>
<div class="mt-4">Marge top très grande</div>

<div class="mb-1">Marge bottom petite</div>
<div class="mb-2">Marge bottom moyenne</div>
<div class="mb-3">Marge bottom grande</div>
<div class="mb-4">Marge bottom très grande</div>

<!-- Padding -->
<div class="p-1">Padding petit</div>
<div class="p-2">Padding moyen</div>
<div class="p-3">Padding grand</div>
<div class="p-4">Padding très grand</div>
```

### Flexbox

```html
<!-- Centrer -->
<div class="flex-center">
    <p>Contenu centré</p>
</div>

<!-- Espace entre -->
<div class="flex-between">
    <span>Gauche</span>
    <span>Droite</span>
</div>

<!-- Avec gaps -->
<div class="flex gap-2">
    <div>Item 1</div>
    <div>Item 2</div>
    <div>Item 3</div>
</div>
```

### Animations

```html
<!-- Apparition en fondu -->
<div class="animate-fade-in">
    <p>Apparaît progressivement</p>
</div>

<!-- Apparition depuis le bas -->
<div class="animate-fade-in-up">
    <p>Monte en apparaissant</p>
</div>

<!-- Pulse infini -->
<button class="btn btn-primary animate-pulse">
    Cliquez-moi !
</button>
```

### Texte

```html
<p class="text-center">Texte centré</p>
<p class="text-left">Texte à gauche</p>
<p class="text-right">Texte à droite</p>
```

---

## 🎨 Variables CSS Personnalisables

Pour personnaliser les couleurs et styles, modifiez les variables dans `:root` :

```css
:root {
    /* Couleurs principales */
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    
    /* Espacements */
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    
    /* Bordures */
    --radius-md: 8px;
    --radius-lg: 12px;
    
    /* Ombres */
    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.15);
}
```

---

## 📖 Exemples d'Utilisation

### Page de Connexion

```html
<main>
    <div class="card" style="max-width: 400px; margin: 0 auto;">
        <div class="card-header">
            <h2 class="text-center">🔐 Connexion</h2>
        </div>
        <div class="card-body">
            <form>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" placeholder="votre@email.com">
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Se connecter
                </button>
            </form>
        </div>
        <div class="card-footer text-center">
            <a href="#">Mot de passe oublié ?</a>
        </div>
    </div>
</main>
```

### Dashboard avec Cartes

```html
<main>
    <h1>📊 Tableau de Bord</h1>
    
    <div class="flex gap-3" style="flex-wrap: wrap;">
        <!-- Carte 1 -->
        <div class="card" style="flex: 1; min-width: 250px;">
            <h3>👥 Équipes</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--primary-color);">24</p>
            <span class="badge badge-success">+12% ce mois</span>
        </div>
        
        <!-- Carte 2 -->
        <div class="card" style="flex: 1; min-width: 250px;">
            <h3>🎮 Énigmes</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--secondary-color);">156</p>
            <span class="badge badge-info">En cours</span>
        </div>
        
        <!-- Carte 3 -->
        <div class="card" style="flex: 1; min-width: 250px;">
            <h3>🏆 Taux de réussite</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--success);">87%</p>
            <span class="badge badge-warning">En hausse</span>
        </div>
    </div>
</main>
```

### Liste avec Badges

```html
<div class="card">
    <div class="card-header">
        <h3>📋 Équipes Actives</h3>
    </div>
    <div class="card-body">
        <div class="flex-between mb-2">
            <span>Équipe Alice</span>
            <span class="badge badge-primary">En cours</span>
        </div>
        <div class="flex-between mb-2">
            <span>Équipe Bob</span>
            <span class="badge badge-success">Terminé</span>
        </div>
        <div class="flex-between mb-2">
            <span>Équipe Charlie</span>
            <span class="badge badge-warning">En attente</span>
        </div>
    </div>
</div>
```

---

## 🎯 Bonnes Pratiques

### ✅ À FAIRE

1. **Utiliser les variables CSS** pour la cohérence
2. **Utiliser les classes utilitaires** plutôt que du CSS inline
3. **Tester sur mobile** régulièrement
4. **Respecter l'accessibilité** (contraste, focus, etc.)
5. **Utiliser les animations avec modération**

### ❌ À ÉVITER

1. ❌ Surcharger avec trop d'animations
2. ❌ Ignorer le mode responsive
3. ❌ Utiliser des couleurs qui ne sont pas dans les variables
4. ❌ Créer des styles inline complexes
5. ❌ Oublier les états hover/focus pour l'accessibilité

---

## 🔧 Personnalisation Avancée

### Changer le thème de couleur

Pour un thème bleu/vert :

```css
:root {
    --primary-color: #3498db;
    --secondary-color: #2ecc71;
    --accent-color: #1abc9c;
}
```

Pour un thème sombre par défaut :

```css
:root {
    --bg-light: #1a1a2e;
    --bg-white: #16213e;
    --text-primary: #e4e4e4;
}
```

### Ajouter une nouvelle couleur

```css
:root {
    --custom-color: #ff6b6b;
}

.btn-custom {
    background: var(--custom-color);
    color: white;
}
```

---

## 📱 Tests Responsive

### Points de rupture

- **Mobile** : < 480px
- **Tablet** : 481px - 768px
- **Desktop** : 769px - 1024px
- **Large Desktop** : > 1024px

### Tester

1. Ouvrir les DevTools du navigateur (F12)
2. Activer le mode responsive
3. Tester sur différentes tailles
4. Vérifier les breakpoints

---

## 🎨 Inspiration du Design

Le design s'inspire de :
- **Material Design** (Google)
- **Tailwind CSS** (Utility classes)
- **Bootstrap** (Composants)
- **Cyberpunk/Tech** (Thématique)

---

## 📚 Ressources

- [CSS Variables (MDN)](https://developer.mozilla.org/fr/docs/Web/CSS/Using_CSS_custom_properties)
- [Flexbox Guide](https://css-tricks.com/snippets/css/a-guide-to-flexbox/)
- [CSS Grid Guide](https://css-tricks.com/snippets/css/complete-guide-grid/)
- [Responsive Design](https://web.dev/responsive-web-design-basics/)

---

**Créé le** : 24 octobre 2025  
**Version** : 1.0  
**Thème** : Cybersécurité & Cryptographie

