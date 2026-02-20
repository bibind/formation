# Atelier 09 — Tests ApiTestCase

## 1. Enjeux (Pourquoi on le fait en production)
- Enjeux qualite: couverture des scenarii critiques.
- Enjeux stabilite: eviter les regressions.
- Risques si mal fait: API instable, regressions non detectees.
- Temps estime: 15 min.

## 2. Objectifs pedagogiques (Bloom)
- Comprendre: structure d'un test ApiTestCase.
- Appliquer: ecrire des tests sur JWT, create, upload, orders.
- Analyser: distinguer happy/error paths.
- Evaluer: fiabilite du pipeline make test.
- Temps estime: 15 min.

## 3. Pre-requis
- Atelier precedent: Atelier 08 (upload).
- Etat du code attendu: endpoints products/orders/upload en place.
- Temps estime: 5 min.

## 4. Contexte metier
- User story: garantir le fonctionnement complet du Marketplace.
- Regles metier: droits, stock, upload, JWT.
- Temps estime: 10 min.

## 5. Pratique guidee (demo formateur)
- Etape 1: creer des fixtures de base (users, categories, products).
- Etape 2: tester login JWT.
- Etape 3: tester create product, order, upload.
- Temps estime: 60 min.

Extrait de code cible:
```php
$this->assertResponseStatusCodeSame(201);
```

Commandes utiles:
```bash
make test
```

## 6. Application (travail stagiaire)
- Checklist:
  - Ajouter les tests principaux dans `MarketplaceApiTest`.
  - Couvrir succes et erreurs.
  - Verifier les codes HTTP.
- Livrables attendus:
  - `project/tests/Api/MarketplaceApiTest.php`
- Definition of Done:
  - `make test` passe avec 0 erreur.
- Temps estime: 60 min.

## 7. Analyse / Debrief (questions + variations)
- Questions:
  - Quelle est la valeur d'un test e2e vs unitaire?
  - Comment isoler les donnees entre tests?
- Variantes:
  - Ajouter des tests pour filters/pagination.
- Recommandations:
  - Garder des tests rapides et deterministes.
- Temps estime: 20 min.

## 8. Tests & validation
- Tests a ecrire (ApiTestCase):
  - `test_auth_jwt_obtain_token`
  - `test_seller_can_create_product`
  - `test_seller_cannot_edit_other_seller_product`
  - `test_order_create_ok_decrements_stock_when_validated`
  - `test_order_create_fails_when_insufficient_stock`
  - `test_upload_image_ok`
  - `test_upload_image_fails_wrong_mime_or_too_large`
- Scenarios:
  - Happy path: create product, order, upload.
  - Error path: stock insuffisant, acces interdit, upload invalide.
- Exemple curl:
```bash
curl -X POST -H "Content-Type: application/json" -d '{"username":"user@example.test","password":"Password123!"}' https://api.example.test/api/login_check
```
- Exemple JSON (reponse):
```json
{ "token": "jwt_token_value" }
```
- Assertions attendues:
  - Codes 200/201/204.
  - Erreurs 400/403.
- Preuves audit:
  - Rapport `make test` OK.
- Temps estime: 30 min.

## 9. Pieges frequents & Debug tips
- Oublier le token JWT dans les tests.
- Tester des donnees non isolees.
- Ne pas purger la base en setUp.
- Debug: afficher la reponse JSON en cas d'echec.
- Debug: verifier `APP_ENV=test`.

## 10. Bonus (avance)
- Ajouter des tests de performance simples.
- Ajouter un test OpenAPI presence endpoint upload.
