# Rapport patch technique – API Platform Avance – Projet Marketplace

## Objectifs
- Tracer les modifications apportees aux squelettes.
- Documenter les hypotheses et limites restantes.

## Fichiers modifies
- `project/src/Application/State/Processor/OrderCreateProcessor.php`
- `project/src/Application/State/Processor/ProductImageUploadProcessor.php`
- `project/src/Infrastructure/Repository/ProductRepository.php`
- `project/src/Infrastructure/Repository/OrderRepository.php`
- `project/src/Application/State/Provider/ProductCollectionProvider.php`
- `project/src/Application/State/Provider/OrderCollectionProvider.php`
- `project/src/Application/Service/ProductFactory.php`
- `project/src/Application/Service/OrderFactory.php`
- `project/src/Application/Dto/ProductInput.php`
- `project/src/Application/Dto/ProductOutput.php`
- `project/src/Domain/User.php`
- `project/src/Domain/Category.php`
- `project/src/Domain/Product.php`
- `project/src/Domain/Order.php`
- `project/src/Domain/OrderLine.php`
- `project/config/packages/vich_uploader.yaml`
- `project/docs/architecture.md`
- `project/docs/endpoints.md`
- `deliverables/05_corriges/corriges_techniques.md`
- `project/tests/Api/MarketplaceApiTest.php`

## Scenarios couverts
- JWT login (token obtenu).
- Creation produit par vendeur authentifie.
- Interdiction edition produit par autre vendeur (403).
- Creation commande avec decrement stock.
- Echec creation commande si stock insuffisant.
- Upload image OK (multipart).
- Upload image invalide (mime non supporte).

## Hypotheses
- Endpoint JWT par defaut: `POST /api/login_check` (LexikJWT).
- Endpoints API Platform: `POST /api/products`, `PUT /api/products/{id}`, `POST /api/orders`.
- Endpoint upload: `POST /api/products/{id}/image`.
- Les entites Domain sont mappees Doctrine (attributs ORM ajoutes) et non exposees.
- Le mapping VichUploader utilise `product_images` et champ `imageName`.
- Les exceptions `DomainException` sont converties en 400 par la couche HTTP (handler d'exception Symfony/API Platform).

## Limites restantes
- Les migrations et fixtures ne sont pas generees ici.

## Criteres de reussite
- Aucune occurrence de placeholder ou `return []` dans le code cible.
- Repositories avec QueryBuilder et pagination Doctrine.
- Processors avec validation metier et decrementation de stock.
- Tests ApiTestCase couvrant succes et echec.

## Pieges frequents
- Oublier de configurer l'exception handler pour `DomainException`.
- Ne pas activer VichUploader dans la config.

## Preuves audit possibles
- `rg` sans placeholder.
- Rapports PHPUnit.
- Logs des uploads en environnement de test.
- Preuve documentaire OpenAPI exportee: `project/docs/openapi.json`.
- Tests associes: `make test` (OK, 7 tests, 10 assertions).
