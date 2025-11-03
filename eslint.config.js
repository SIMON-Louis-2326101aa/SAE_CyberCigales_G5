// eslint.config.js
import eslint from '@eslint/js';
import globals from 'globals';

export default [
    {
        ignores: ["vendor/**", "node_modules/**"]
    },
    eslint.configs.recommended,
    {
        languageOptions: {
            globals: {
                ...globals.browser, // Pour le code s'exécutant dans le navigateur
                ...globals.node,   // Pour les scripts côté serveur
            },
            ecmaVersion: 'latest',
            sourceType: 'module'
        }
    },
    // Ajoutez ici d'autres configurations ou règles spécifiques si besoin
];