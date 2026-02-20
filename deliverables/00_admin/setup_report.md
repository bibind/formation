# Setup Report – API Platform Avance Marketplace

## Resume environnement
- OS: Ubuntu (sandbox)
- PHP: 8.2.30 (CLI)
- Composer: 2.2.6
- Docker: 28.3.3
- Docker Compose: v2.39.1
- Symfony: 7.4.5 (framework-bundle)

## Extensions PHP detectees
- pdo_pgsql, openssl, sodium, intl, gd, mbstring, xml, zip

## Packages principaux installes
- api-platform/api-pack: v1.4.0
- api-platform/*: v4.2.16
- doctrine/orm: 3.6.2
- doctrine/doctrine-migrations-bundle: 3.7.0
- lexik/jwt-authentication-bundle: v3.2.0
- vich/uploader-bundle: v2.9.1
- phpunit/phpunit: 11.5.55
- symfony/test-pack: v1.2.0

## Resultat des tests
- `make test`: OK (7 tests, 10 assertions)

## Erreurs corrigees automatiquement
- Configuration VichUploader (namer manquant) ajoutee.
- Mismatch setter `imageName` corrige dans `App\Domain\Product`.
- Upload multipart valide avec `deserialize: false` dans le processor.
- Verifications MIME renforcees (client + serveur).
- Test stock: relecture entite pour eviter `refresh` sur entite detachee.

## Points d'attention restants
- `JWT_PASSPHRASE` est une valeur par defaut a remplacer en environnement reel.
- Les migrations Doctrine restent a generer si le projet doit tourner sans `SchemaTool`.

## Execution make ci
- Resultat: OK (7 tests, 10 assertions)
- Etapes: `make up`, verification deps Composer, `make jwt-check`, reset DB test, `make test`

## Execution make reset-all
- Resultat: OK
- Actions: `docker compose down -v`, purge cache/logs, `make up`, `make install`
- Correctif applique: auto-scripts Composer passes sur `@php bin/console` (suppression dependance `symfony-cmd`)

## Execution make ci (apres reset-all)
- Resultat: OK (7 tests, 10 assertions)
- Correctif applique: creation explicite de la base `app_test` via `db-test-ensure`

## Execution make reset-all + make ci (demande utilisateur)
- Resultat reset-all: OK
- Resultat make ci: OK (7 tests, 10 assertions)

## Export OpenAPI (preuve)
- Commande: `APP_ENV=test php /tmp/openapi_http_export.php`
- Fichier genere: `project/docs/openapi.json`
- Resultat: OK (endpoint upload present)
- Note: export via `/api/docs.jsonopenapi` avec JWT (docs proteges par `ROLE_USER`).
