# Atelier 02 — Provider collection Product

## 1. Enjeux (Pourquoi on le fait en production)
- Enjeux techniques: controler la lecture des collections et la pagination.
- Enjeux perf: eviter le N+1 via join + addSelect.
- Risques si mal fait: lenteur, consommation memoire, timeouts API.
- Temps estime: 15 min.

## 2. Objectifs pedagogiques (Bloom)
- Comprendre: le role d'un Provider en API Platform.
- Appliquer: implementer un Provider de collection.
- Analyser: mesurer l'impact des joins sur les requetes.
- Evaluer: verifier pagination/tri.
- Temps estime: 15 min.

## 3. Pre-requis
- Atelier precedent: Atelier 01 (DTO exposes).
- Etat du code attendu: `ProductOutput` expose et endpoint GET /api/products disponible.
- Temps estime: 5 min.

## 4. Contexte metier
- User story: en tant qu'utilisateur, je veux lister les produits avec leur categorie.
- Regles metier: la liste respecte la pagination standard.
- Temps estime: 10 min.

## 5. Pratique guidee (demo formateur)
- Etape 1: creer `ProductCollectionProvider`.
- Etape 2: requete Doctrine avec join category + addSelect.
- Etape 3: mapper vers `ProductOutput`.
- Temps estime: 45 min.

Extrait de code cible:
```php
$qb = $this->productRepository->createQueryBuilder('p')
    ->leftJoin('p.category', 'c')
    ->addSelect('c');
```

Commandes utiles:
```bash
php project/bin/console doctrine:database:create
```

## 6. Application (travail stagiaire)
- Checklist:
  - Implementer le Provider.
  - Ajouter pagination et tri si necessaire.
  - Verifier la sortie JSON.
- Livrables attendus:
  - `project/src/Application/State/Provider/ProductCollectionProvider.php`
- Definition of Done:
  - Collection accessible et paginee.
  - Pas de N+1 sur category.
- Temps estime: 45 min.

## 7. Analyse / Debrief (questions + variations)
- Questions:
  - Quand utiliser un Provider vs un Repository direct?
  - Quels impacts si on ajoute trop d'associations?
- Variantes:
  - Ajouter un filtre par categorie.
- Recommandations:
  - Limiter les joins au strict necessaire.
- Temps estime: 20 min.

## 8. Tests & validation
- Tests a ecrire (ApiTestCase):
  - GET /api/products retourne la liste paginee.
- Scenarios:
  - Happy path: page=1 retourne un tableau de `ProductOutput`.
  - Error path: page invalide renvoie 400/422 selon config.
- Exemple curl:
```bash
curl -X GET "https://api.example.test/api/products?page=1"
```
- Exemple JSON (reponse):
```json
[
  { "id": "prod_123", "name": "Mug", "categoryName": "Mugs", "stock": 10, "price": 1500 }
]
```
- Assertions attendues:
  - Code 200.
  - Liste non vide si fixtures presentes.
- Preuves audit:
  - Logs SQL (join + addSelect).
  - OpenAPI exporte (`project/docs/openapi.json`).
- Temps estime: 25 min.

## 9. Pieges frequents & Debug tips
- Oublier `addSelect` -> N+1.
- Ignorer la pagination -> reponse trop lourde.
- Mapper un champ non charge.
- Debug: activer le log SQL Doctrine.
- Debug: verifier les headers de pagination.

## 10. Bonus (avance)
- Ajouter un cache HTTP sur la collection.
- Ajouter un tri par prix.
