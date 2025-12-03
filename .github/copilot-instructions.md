<!-- .github/copilot-instructions.md — Guidage pour agents IA travaillant sur ce dépôt -->
# Instructions rapides pour les agents IA

Résumé court
- Projet Laravel (PHP ^8.2, Laravel 11) avec une UI front React (dans `api_front/public/app`) et un panneau d'administration basé sur Filament.
- Frontend build via `laravel-mix` (voir `webpack.mix.js`), back-end standard Laravel avec commandes artisan personnalisées.

Ce que l'agent doit savoir en premier
- Lire `composer.json` pour dépendances (Filament, Scout/Meilisearch, Sanctum, Fortify).
- Lire `webpack.mix.js` pour comprendre les alias JS (`@app`, `@components`, etc.) et la logique d'environnement (fichiers `.env.*` dans `api_front/public/app/`).
- L'admin panel est géré avec Filament — voir `app/Providers/Filament/AdminPanelProvider.php` (branche: `exchange-or-features-for-filamentphp`).

Commandes de développement importantes
- Installer backend: `composer install` (attention aux dépendances Google Drive ; README décrit contournement Windows pour le timeout).
- Installer frontend: `npm install` depuis la racine (les scripts sont dans `package.json`).
- Compiler assets (exemples):
  - développement : `npm run dev`
  - production/preprod : `npm run prod` ou `npm run pre-prod`
- Commandes artisan utiles (décrites dans `README.md`):
  - `php artisan key:generate`
  - `php artisan ide-helper:generate` (IDE helper)
  - `php artisan project:fresh_db` (réinitialise + seed — destructive)
  - `php artisan project:init_storage` (crée arborescence Google Drive)
  - `php artisan project:init` (réinitialisation DB alternative)

Conventions et patterns projet
- PSR-4 mapping personnalisé (voir `composer.json` `autoload`):
  - `App\` -> `app/`
  - `Api\` -> `api_front/api/` (API côté front)
  - `School\` -> `school/`
  - `Models\` -> `Models/` (les modèles principaux sont ici)
- Frontend React : code dans `api_front/public/app`, les builds écrivent dans `public/`.
- Variables d'environnement front end : `api_front/public/app/.env.{environment}` (chargées par `webpack.mix.js`).
- Stockage cloud : adapter Google Drive ; le projet attend un fichier `creds.json` (ou `credentialsofserviceaccount.json`) à la racine — voir `README.md`.
- Recherche : Laravel Scout + Meilisearch. Dossier `meili_data/` contient configuration pour exécution locale (Docker + Windows instructions dans README).

Points d'intégration et d'attention
- Filament : plusieurs commandes de post-install (voir scripts composer) incluent `@php artisan filament:upgrade` — attention aux migrations du panneau d'administration.
- Scripts composer post-update publient les assets et génèrent l'IDE helper (`vendor:publish`, `ide-helper:generate`).
- Webpack/Mix : compression d'images via `imagemin-webpack-plugin` — builds CI pourraient devoir exécuter avec Node 16 (README recommande Node v16.9).
- DB locale : le README montre `DB_PORT=3307` (Laragon) — ne pas présumer du port par défaut.

Tests et CI
- Pas de tests unitaires maintenus dans ce dépôt. Ne pas écrire ni compter sur des tests unitaires automatiques.
- Pour exécuter les tests existants (si présents) : `php artisan test`.
- `phpunit.xml` existe et contient des variables d'environnement utiles (`CACHE_DRIVER=array`, `QUEUE_CONNECTION=sync`) — utiliser ces réglages si vous exécutez des tests d'intégration/feature.

Exemples concrets à utiliser par l'agent
- Si vous cherchez la logique d'administration : ouvrir `app/Providers/Filament/AdminPanelProvider.php` et `app/Actions/`.
- Pour comprendre l'API front-end, inspecter `api_front/api/routes.php` et `api_front/public/app` (aliases et points d'entrée dans `webpack.mix.js`).
- Pour trouver seeders et fixtures : `database/seeders/` et `storage/` (fichiers CSV utilisés par seeders, voir README).

Ce que l'agent ne doit pas faire automatiquement
- Ne pas lancer de commandes destructives sans confirmation explicite (ex: `project:fresh_db`, `project:init`).
- Ne pas modifier les clés d'environnement sensibles ni pousser de `creds.json`/fichiers de clé.

Questions à poser au développeur (si incertain)
- Quel est l'URL local habituel (ex: `https://qleiade.test`) et la configuration Laragon souhaitée ?
- La migration vers Filament est-elle terminée ou doit-on conserver du code legacy à supprimer progressivement ?
- Y a‑t‑il un workflow CI/CD (build, tests, déploiement) à respecter pour les PRs ?

Fichiers de référence à consulter en priorité
- `README.md` (racine)
- `composer.json`, `package.json`, `webpack.mix.js`
- `phpunit.xml`
- `app/Providers/Filament/AdminPanelProvider.php`
- `api_front/public/app/.env.*` (exemples d'env front)

Fin
> Après validation, je peux affiner ou ajouter sections (CI, conventions de commit, règles de code) selon vos retours.
