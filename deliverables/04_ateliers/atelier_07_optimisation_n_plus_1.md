# Atelier 07 — Optimisation N+1 sur lecture commandes

## 1. Enjeux (Pourquoi on le fait en production)
- Enjeux perf: limiter les requetes SQL pour les collections.
- Enjeux maintenabilite: requetes explicites et previsibles.
- Risques si mal fait: explosions de requetes, temps de reponse eleve.
- Temps estime: 15 min.

## 2. Objectifs pedagogiques (Bloom)
- Comprendre: symptomes N+1 sur les commandes.
- Appliquer: fetch joins et pagination adaptee.
- Analyser: mesurer les gains.
- Evaluer: valider avec logs SQL.
- Temps estime: 15 min.

## 3. Pre-requis
- Atelier precedent: Atelier 06 (filtres).
- Etat du code attendu: Provider OrderCollection existant.
- Temps estime: 5 min.

## 4. Contexte metier
- User story: un client consulte l'historique de ses commandes et lignes.
- Regles metier: isolation par utilisateur.
- Temps estime: 10 min.

## 5. Pratique guidee (demo formateur)
- Etape 1: identifier le N+1 sur Order -> OrderLine.
- Etape 2: ajouter fetch join sur lines et products si utile.
- Etape 3: garder la pagination fonctionnelle.
- Temps estime: 45 min.

Extrait de code cible:
```php
$qb->leftJoin('o.lines', 'l')
   ->addSelect('l');
```

Commandes utiles:
```bash
php project/bin/console doctrine:migrations:migrate
```

## 6. Application (travail stagiaire)
- Checklist:
  - Ajuster le Provider ou Repository des commandes.
  - Ajouter fetch join minimal.
  - Verifier pagination et performances.
- Livrables attendus:
  - `project/src/Application/State/Provider/OrderCollectionProvider.php`
- Definition of Done:
  - Nombre de requetes reduit (preuve via logs).
- Temps estime: 45 min.

## 7. Analyse / Debrief (questions + variations)
- Questions:
  - Quels risques si on join trop?
  - Comment eviter la duplication de lignes?
- Variantes:
  - Projection DTO specifique pour liste.
- Recommandations:
  - Mesurer avant d'optimiser.
- Temps estime: 20 min.

## 8. Tests & validation
- Tests a ecrire (ApiTestCase):
  - GET /api/orders retourne commandes + lignes.
- Scenarios:
  - Happy path: collection paginee chargee en peu de requetes.
- Exemple curl:
```bash
curl -X GET \
  -H "Authorization: Bearer <token>" \
  https://api.example.test/api/orders
```
- Exemple JSON (reponse):
```json
[
  { "id": "ord_123", "total": 3000, "lines": [ { "productId": "prod_123", "quantity": 2 } ] }
]
```
- Assertions attendues:
  - Code 200.
  - `lines` remplies.
- Preuves audit:
  - Logs SQL avant/apres.
- Temps estime: 20 min.

## 9. Pieges frequents & Debug tips
- Fetch join qui casse la pagination.
- Duplicats dans la collection.
- Charger des associations non necessaires.
- Debug: activer le SQL logger.
- Debug: comparer le nombre de requetes.

## 10. Bonus (avance)
- Ajouter un DataLoader ou cache par commande.
- Ajouter un endpoint detail vs collection.
