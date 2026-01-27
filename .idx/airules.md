# Instructions rapides pour les agents IA

Résumé court
- Projet Laravel (PHP ^8.2, Laravel 11) avec une UI front React (dans `api_front/public/app`) et un panneau d'administration basé sur Filament.
- Frontend build via `laravel-mix` (voir `webpack.mix.js`), back-end standard Laravel avec commandes artisan personnalisées.
- **Langue** : Toujours répondre en français pour chaque nouvelle conversation, aussi bien dans le chat de discussion que dans tous les fichiers générés (plans d'implémentation, bilan/walkthrough, etc.).

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

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.2.23
- filament/filament (FILAMENT) - v5
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v11
- laravel/mcp (MCP) - v0
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- laravel/scout (SCOUT) - v10
- laravel/socialite (SOCIALITE) - v5
- livewire/livewire (LIVEWIRE) - v4
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v10
- eslint (ESLINT) - v7
- laravel-echo (ECHO) - v1
- react (REACT) - v17

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.
- **Répondre systématiquement en français.** Cela s'applique au chat ainsi qu'à tous les documents produits (fichiers markdown de plan, de walkthrough, de tâche, etc.).

## Documentation Files
- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs
- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches when dealing with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The `search-docs` tool is perfect for all Laravel-related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless there is something very complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v11 rules ===

## Laravel 11

- Use the `search-docs` tool to get version-specific documentation.
- This project upgraded from Laravel 10 without migrating to the new streamlined Laravel 11 file structure.
- This is **perfectly fine** and recommended by Laravel. Follow the existing structure from Laravel 10. We do not need to migrate to the Laravel 11 structure unless the user explicitly requests it.

### Laravel 10 Structure
- Middleware typically lives in `app/Http/Middleware/` and service providers in `app/Providers/`.
- There is no `bootstrap/app.php` application configuration in a Laravel 10 structure:
    - Middleware registration is in `app/Http/Kernel.php`
    - Exception handling is in `app/Exceptions/Handler.php`
    - Console commands and schedule registration is in `app/Console/Kernel.php`
    - Rate limits likely exist in `RouteServiceProvider` or `app/Http/Kernel.php`

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

### New Artisan Commands
- List Artisan commands using Boost's MCP tool, if available. New commands available in Laravel 11:
    - `php artisan make:enum`
    - `php artisan make:class`
    - `php artisan make:interface`

=== livewire/core rules ===

## Livewire

- Use the `search-docs` tool to find exact version-specific documentation for how to write Livewire and Livewire tests.
- Use the `php artisan make:livewire [Posts\CreatePost]` Artisan command to create new components.
- State should live on the server, with the UI reflecting it.
- All Livewire requests hit the Laravel backend; they're like regular HTTP requests. Always validate form data and run authorization checks in Livewire actions.

## Livewire Best Practices
- Livewire components require a single root element.
- Use `wire:loading` and `wire:dirty` for delightful loading states.
- Add `wire:key` in loops:

    ```blade
    @foreach ($items as $item)
        <div wire:key="item-{{ $item->id }}">
            {{ $item->name }}
        </div>
    @endforeach
    ```

- Prefer lifecycle hooks like `mount()`, `updatedFoo()` for initialization and reactive side effects:

<code-snippet name="Lifecycle Hook Examples" lang="php">
    public function mount(User $user) { $this->user = $user; }
    public function updatedSearch() { $this->resetPage(); }
</code-snippet>

## Testing Livewire

<code-snippet name="Example Livewire Component Test" lang="php">
    Livewire::test(Counter::class)
        ->assertSet('count', 0)
        ->call('increment')
        ->assertSet('count', 1)
        ->assertSee(1)
        ->assertStatus(200);
</code-snippet>

<code-snippet name="Testing Livewire Component Exists on Page" lang="php">
    $this->get('/posts/create')
    ->assertSeeLivewire(CreatePost::class);
</code-snippet>

=== phpunit/core rules ===

## PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).
</laravel-boost-guidelines>
